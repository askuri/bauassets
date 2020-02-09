<?php

namespace Tests\Feature;

use App\Loan;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @covers \App\Http\Controllers\AssetLoanController
 */
class AssetLoanTest extends TestCase
{
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
     * Test if unauthorized user can attach assets to loans
     */
    public function testStoreUnauthorized() {
        $response = $this->post(route('assetsloans.store'), [
            'loan_id' => $this->loan_noasset->id,
            'asset_search' => 'foo',
        ]);
        $response->assertForbidden();
    }
    
    /**
     * Test if authorized users get proper error handling when trying
     * to attach an asset that could not be found.
     */
    public function testStoreAuthorizedNotFound() {
        $response = $this->actingAs($this->user_moderator)
                ->post(route('assetsloans.store'), [
            'loan_id' => $this->loan_withassets_notimmutable->id,
            'asset_search' => 'trust me i don\'t exist',
        ]);
        // redirects back(), don't test for correct route
        $response->assertRedirect();
        $response->assertSessionHasErrors(['asset_search']);
    }
    
    /**
     * Test if assets can be stored to immutable loans.
     */
    public function testStoreAuthorizedImmutable() {
        $response = $this->actingAs($this->user_moderator)
                ->post(route('assetsloans.store'), [
            'loan_id' => $this->loan_withassets->id,
            'asset_search' => 'dont care',
        ]); 
        
        $response->assertForbidden();
    }
    
    /**
     * Test if an authorized user can attach assets to loans successfully.
     */
    public function testStoreAuthorizedSuccessful() {
        $asset = $this->loan_withassets->assets[0];
        
        $response = $this->actingAs($this->user_moderator)
                ->post(route('assetsloans.store'), [
            'loan_id' => $this->loan_withassets_notimmutable->id,
            'asset_search' => $asset->assetnames[0]->name,
        ]);
        $response->assertRedirect();
        $response->assertSessionDoesntHaveErrors();
        
        // test if it was added successfully.
        // cannot use followingRedirects() because of back() in controller
        
        $response2 = $this->actingAs($this->user_moderator)
                ->get(route('loans.edit', $this->loan_withassets_notimmutable->id));
        $response2->assertOk();
        $response2->assertSee($asset->assetnames[0]->name);
    }
    
    /**
     * Test if unauthorized users can detach assets from loans.
     */
    public function testDestroyUnauthorized() {
        $asset = $this->loan_withassets->assets[0];
        
        $response = $this->delete(route('assetsloans.destroy'), [
            'loan_id' => $this->loan_withassets_notimmutable->id,
            'asset_id' => $asset->id,
        ]);
        $response->assertForbidden();
    }
    
    /**
     * Test if assets can be detached from immutable loan
     */
    public function testDestroyAuthorizedImmutable() {
        $asset = $this->loan_withassets->assets[0];
        
        $response = $this->actingAs($this->user_moderator)
                ->delete(route('assetsloans.destroy'), [
            'loan_id' => $this->loan_withassets->id,
            'asset_id' => $asset->id,
        ]);
        $response->assertForbidden();
    }
    
    /**
     * Test if authorized users can detach assets from loans.
     */
    public function testDestroyAuthorizedSuccessful() {
        $asset = $this->loan_withassets->assets[0];
        
        $response = $this->actingAs($this->user_moderator)
                ->delete(route('assetsloans.destroy'), [
            'loan_id' => $this->loan_withassets_notimmutable->id,
            'asset_id' => $asset->id,
        ]);
        $response->assertRedirect();
        $response->assertSessionDoesntHaveErrors();
        
        // test if it was removed successfully.
        // cannot use followingRedirects() because of back() in controller
        
        $response2 = $this->actingAs($this->user_moderator)
                ->get(route('loans.destroy', $this->loan_withassets_notimmutable->id));
        $response2->assertOk();
        $response2->assertDontSeeText($asset->assetnames[0]->name);
    }
}
