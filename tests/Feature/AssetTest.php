<?php

namespace Tests\Feature;

use App\Loan;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @covers \App\Http\Controllers\AssetController
 */
class AssetTest extends TestCase
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
     * Test if showBySearch with an unsuccessful search result
     */
    public function testShowBySearchUnsuccessful() {
        //$this->followingRedirects(); // follow redirects for this test
        
        $response = $this->get(route('assets.show_by_search'), [
            'asset_search' => 'i hope this wont ever exist',
        ]);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['asset_search']);
    }
    
    /**
     * Test if showBySearch with a successful search result
     */
    public function testShowBySearchSuccessful() {
        $this->followingRedirects(); // follow redirects for this test
        
        $asset = $this->loan_withassets->assets[0]; // asset that we want to search
        $response = $this->get(route('assets.show_by_search') .'?asset_search='.$asset->assetnames[0]->name);
        $response->assertOk();
        $response->assertSeeText($asset->assetnames[0]->name);
        
    }
    
    /**
     * Test if asset is shown correctly if it has loans associated
     */
    public function testShowHasLoans() {
        $response = $this->get(route('assets.show', $this->loan_withassets->assets[0]->id));
        $response->assertOk();
        
        foreach ($this->loan_withassets->assets[0]->assetnames as $assetname) {
            $response->assertSeeText($assetname->name);
        }
        $response->assertSeeText($this->loan_withassets->borrower_name);
        $response->assertSeeText($this->loan_withassets->borrower_room);
        $response->assertSeeText($this->loan_withassets->issuer->name);
    }
}
