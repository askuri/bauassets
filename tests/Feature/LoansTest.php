<?php

namespace Tests\Feature;

use App\Loan;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * @covers \App\Http\Controllers\LoanController
 */
class LoansTest extends TestCase
{
    /*
     * This seems to be an old trait and should be used anymore.
     * Now we use RefreshDatabase and the in-memory db also works fine.
     */
    //use DatabaseMigrations;
    use RefreshDatabase;
    
    private $loan_noasset;
    private $loan_withassets;
    private $loan_withassets_notimmutable;
    
    private $user;
    private $user_moderator;
    
    public function setUp(): void {
        parent::setUp();
        
        $this->loan_noasset = factory(Loan::class)->create();
        $this->loan_withassets = factory(Loan::class)->state("with_assets")->create();
        $this->loan_withassets_notimmutable = factory(Loan::class)->states("with_assets", "not_immutable")->create();
        
        $this->user = factory(User::class)->create();
        $this->user_moderator = factory(User::class)->state('role_moderator')->create();
    }
    
    /**
     * Check if the response shows the names of the assets contained in
     * the collection.
     * 
     * @param \Illuminate\Foundation\Testing\TestResponse $response
     * @param \Illuminate\Database\Eloquent\Collection $assets Collection of App\Asset
     */
    public function helperTestAssetNamesShown(\Illuminate\Foundation\Testing\TestResponse $response, \Illuminate\Database\Eloquent\Collection $assets) {
        foreach ($assets as $asset) {
            foreach ($asset->assetnames as $assetname) {
                $response->assertSee($assetname->name);
            }
        }
    }
    
    /**
     * Test if the index displays loans that were just added
     * with user being unauthenticated
     *
     * @return void
     */
    public function testIndexUnauthenticated()
    {
        //$this->withoutExceptionHandling();
        $response = $this->get(route('loans.index'));

        $response->assertSuccessful();
        
        $response->assertSee($this->loan_noasset->borrower_name);
        $response->assertSee($this->loan_noasset->borrower_room);
        $response->assertSee(route('loans.show', $this->loan_noasset->id));
    }
    
    /**
     * Test if the index displays loans that were just added
     * with user being authenticated
     *
     * @return void
     */
    public function testIndexAuthenticated()
    {
        //$this->withoutExceptionHandling();
        $response = $this->actingAs($this->user_moderator)
                ->get(route('loans.index'));

        $response->assertSuccessful();
        
        $response->assertSee($this->loan_noasset->borrower_name);
        $response->assertSee($this->loan_noasset->borrower_room);
        $response->assertSee(route('loans.edit', $this->loan_noasset->id));
    }
    
    /**
     * Tests if a loan without assets is shown properly
     *
     * @return void
     * @todo check if "no assets" message is shown
     */
    public function testShowWithoutAssets()
    {
        $response = $this->get(route('loans.show', $this->loan_noasset->id));

        $response->assertSuccessful();
        
        $response->assertSee($this->loan_noasset->issuer->name);
        $response->assertSee($this->loan_noasset->borrower_name);
        $response->assertSee($this->loan_noasset->borrower_room);
        $response->assertSee($this->loan_noasset->comment);
    }
    
    /**
     * Tests if a loan with assets is shown properly
     *
     * @return void
     */
    public function testShowWithAssets()
    {
        $response = $this->get(route('loans.show', $this->loan_withassets->id));

        $response->assertSuccessful();
        
        // for each asset, each assetname should be displayed somewhere
        $this->helperTestAssetNamesShown($response, $this->loan_withassets->assets);
    }
    
    /**
     * Tests if creating a loan as an unauthorized user is possible
     */
    public function testStoreLoanUnauthorized() {
        $response = $this->actingAs($this->user)
                ->post(route('loans.store'));
        
        $response->assertForbidden();
    }
    
    /**
     * Tests if creating a loan as an authorized user
     * without any input is possible
     */
    public function testStoreLoanAuthorizedNoInput() {
        $response = $this->actingAs($this->user_moderator)
                ->post(route('loans.store'));
        
        // after a successful creation, user should see the edit form to
        // make further modifications
        // Note: explicitly not testing for correct url because that would
        // require us to have the ID of the new loan
        $response->assertRedirect();
        
        $response->assertSessionHasErrors();
    }
    
    /**
     * Tests if creating a loan as an authorized user
     * with input is possible
     */
    public function testStoreLoanAuthorizedWithInput() {
        $response = $this->actingAs($this->user_moderator)
                ->post(route('loans.store'), [
                    'borrower_name' => 'Testname',
                    'borrower_room' => '101',
                    'borrower_email' => 'info@example.com',
                ]);
        
        // after a successful creation, user should see the edit form to
        // make further modifications
        // Note: explicitly not testing for correct url because that would
        // require us to have the ID of the new loan
        $response->assertRedirect();
        
        $response->assertSessionDoesntHaveErrors();
    }
    
    /**
     * Tests if unauthorized users can view the edit form
     */
    public function testEditLoanUnauthorized() {
        $response = $this->get(route('loans.edit', $this->loan_withassets));
        
        $response->assertForbidden();
    }
    
    /**
     * Tests if authorized users can view the edit form
     * @todo status dates not tested
     */
    public function testEditLoanAuthorized() {
        $response = $this->actingAs($this->user_moderator)
                ->get(route('loans.edit', $this->loan_noasset->id));
        
        $response->assertOk();
        
        $response->assertSee($this->loan_noasset->issuer->name);
        $response->assertSee($this->loan_noasset->borrower_name);
        $response->assertSee($this->loan_noasset->borrower_room);
        $response->assertSee($this->loan_noasset->borrower_email);
        $response->assertSee($this->loan_noasset->comment);
        $this->helperTestAssetNamesShown($response, $this->loan_noasset->assets);
    }
    
    /**
     * Test if an unauthorized user can update a loan
     */
    public function testUpdateLoanUnauthorized() {
        $response = $this->put(route('loans.update', $this->loan_noasset));
        $response->assertForbidden();
    }
    
    /**
     * Test if an authorized user can update a loan with
     * correct input and if the changes are correctly persisted.
     * 
     * @todo use $this->followingRedirects() instead of making to requests
     */
    public function testUpdateLoanAuthorizedWithInput() {
        $response = $this->actingAs($this->user_moderator)
                ->put(route('loans.update', $this->loan_withassets_notimmutable), [
                    'borrower_name' => $this->loan_withassets_notimmutable->borrower_name . 'a',
                    'borrower_room' => $this->loan_withassets_notimmutable->borrower_room + 1,
                    'borrower_email' => $this->loan_withassets_notimmutable->borrower_email . 'a',
                    'comment' => $this->loan_withassets_notimmutable->comment . 'a',
                ]);
        $response->assertRedirect(route('loans.edit', $this->loan_withassets_notimmutable->id));
        $response->assertSessionDoesntHaveErrors();
        
        // request successfully made, now let's see if changes were persisted.
        
        $response2 = $this->actingAs($this->user_moderator)
                ->get(route('loans.edit', $this->loan_withassets_notimmutable->id));
        $response2->assertOk();
        $response2->assertSee($this->loan_withassets_notimmutable->borrower_name . 'a');
        $response2->assertSee($this->loan_withassets_notimmutable->borrower_room + 1);
        $response2->assertSee($this->loan_withassets_notimmutable->borrower_email . 'a');
        $response2->assertSee($this->loan_withassets_notimmutable->comment . 'a');
    }
    
    /**
     * Test if immutable loan can be updated
     */
    public function testUpdateImmutableLoanAuthorized() {
        $response = $this->actingAs($this->user_moderator)
                ->put(route('loans.update', $this->loan_withassets), [
                    'borrower_name' => 'need some',
                    'borrower_room' => 104,
                    'borrower_email' => 'dummy_data@here_because.of',
                    'comment' => 'validation in controller',
                ]);
        $response->assertForbidden(); // forbidden means we can't update an immutable loan
    }
}
