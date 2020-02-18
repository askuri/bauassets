<?php

namespace Tests\Feature;

use App\Loan;
use App\User;
use App\Category;
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
    
    private $category;
    
    private $user;
    private $user_moderator;
    
    public function setUp(): void {
        parent::setUp();
        
        $this->loan_noasset = factory(Loan::class)->create();
        $this->loan_withassets = factory(Loan::class)->state("with_assets")->create();
        $this->loan_withassets_notimmutable = factory(Loan::class)->states("with_assets", "not_immutable")->create();
        
        $this->category = factory(Category::class)->create();
        
        $this->user = factory(User::class)->create();
        $this->user_moderator = factory(User::class)->state('role_moderator')->create();
    }
    
    /**
     * Test if assets are listed in index.
     * Not testing for all assets.
     */
    public function testIndex() {
        $response = $this->get(route('assets.index'));
        foreach ($this->loan_withassets->assets as $asset) {
            $response->assertSeeText($asset->getNamesString());
            $response->assertSeeText($asset->category->name);
            $response->assertSeeText($asset->stock);
            $response->assertSeeText($asset->location);
        }
    }
    
    /**
     * Test if an unauthorized user can see the create asset form
     */
    public function testCreateAssetUnauthorized() {
        $response = $this->get(route('assets.create'));
        $response->assertForbidden();
    }
    
    /**
     * Test if an authorized user can see the create asset form
     */
    public function testCreateAssetAuthorized() {
        $response = $this->actingAs($this->user_moderator)
                ->get(route('assets.create'));
        $response->assertOk();
    }
    
    /**
     * Test if an unauthorized user can store an asset
     */
    public function testStoreAssetUnauthorized() {
        $response = $this->post(route('assets.store'), [
            'location' => 'somewhere',
            'category' => 1,
            'stock' => 1,
            'assetnames_lang' => ['en'],
            'assetnames_name' => ['adsf'],
        ]);
        $response->assertForbidden();
    }
    
    /**
     * Test if an authorized user can store an asset
     * and if it is saved correctly.
     */
    public function testStoreAssetAuthorized() {
        $this->followingRedirects();
        // store
        $response = $this->actingAs($this->user_moderator)
                ->post(route('assets.store'), [
            'location' => 'location somewhere',
            'category' => $this->category->id,
            'stock' => 3,
            'assetnames_lang' => ['en'],
            'assetnames_name' => ['some long name'],
        ]);
        $response->assertOk();
        
        // check if stored correctly
        $response->assertSeeText('location somewhere');
        $response->assertSeeText($this->category->name);
        $response->assertSeeText(3);
        $response->assertSeeText('some long name');
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
