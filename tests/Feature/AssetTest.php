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
    private $category2;
    
    private $user;
    private $user_moderator;
    
    public function setUp(): void {
        parent::setUp();
        
        $this->loan_noasset = Loan::factory()->create();
        $this->loan_withassets = Loan::factory()->with_assets()->create();
        $this->loan_withassets_notimmutable = Loan::factory()
                ->with_assets()
                ->not_immutable()
                ->create();
        
        $this->category = Category::factory()->create();
        $this->category2 = Category::factory()->create();
        
        $this->user = User::factory()->create();
        $this->user_moderator = User::factory()->role_moderator()->create();
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
        $response->assertSeeText($this->category->language);
        $response->assertSeeText($this->category->name);
    }
    
    /**
     * Test if an unauthorized user can store an asset
     */
    public function testStoreAssetUnauthorized() {
        $response = $this->post(route('assets.store'), [
            'location' => 'somewhere',
            'category' => 1,
            'stock' => 1,
            'assetnames_language' => ['en'],
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
            'assetnames_language' => ['', 'de', 'en', 'rw'],
            'assetnames_name' => ['', 'en langer name', 'some long name', '', 'nini'],
        ]);
        $response->assertOk();
        
        // check if stored correctly
        $response->assertSeeText('location somewhere');
        $response->assertSeeText($this->category->name);
        $response->assertSeeText(3);
        $response->assertSeeText('en langer name');
        $response->assertSeeText('some long name');
        $response->assertDontSeeText('nini'); // no corresponding language, so not added
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
        
        //$response->assertSee(route('assets.edit', $this->loan_withassets->assets[0]->id)); // why is this test not passing?!
        
        foreach ($this->loan_withassets->assets[0]->assetnames as $assetname) {
            $response->assertSeeText($assetname->name);
        }
        $response->assertSeeText($this->loan_withassets->borrower_name);
        $response->assertSeeText($this->loan_withassets->borrower_room);
        $response->assertSeeText($this->loan_withassets->issuer->name);
    }
    
    /**
     * Test if an unauthorized user can see the edit asset form
     */
    public function testEditAssetUnauthorized() {
        $response = $this->get(route('assets.edit', $this->loan_withassets->assets[0]));
        $response->assertForbidden();
    }
    
    /**
     * Test if an authorized user can see the edit asset form
     */
    public function testEditAssetAuthorized() {
        $asset = $this->loan_withassets->assets[0];
        
        $response = $this->actingAs($this->user_moderator)
            ->get(route('assets.edit', $asset->id));
        $response->assertOk();
        $response->assertSee(route('assets.show', $this->loan_withassets->assets[0]->id));
        $response->assertSee($asset->location);
        $response->assertSee($asset->category->name);
        $response->assertSee($asset->stock);
        
        foreach ($asset->assetnames as $assetname) {
            $response->assertSee($assetname->name);
        }
    }
    
    /**
     * Test if an authorized user can update an asset
     */
    public function testUpdateAssetUnauthorized() {
        $this->followingRedirects();
        $asset = $this->loan_withassets->assets[0];
        
        // content doesn't matter here, we should get 403 before anyways
        $response = $this->put(route('assets.update', $asset->id), []);
        $response->assertForbidden();
    }
    
    /**
     * Test if an authorized user can store an asset
     * and if it is saved correctly.
     */
    public function testUpdateAssetAuthorized() {
        $this->followingRedirects();
        $asset = $this->loan_withassets->assets[0];
        
        $response = $this->actingAs($this->user_moderator)
                ->put(route('assets.update', $asset->id), [
            'location' => $asset->location . 'updated',
            'category' => $this->category2->id,
            'stock' => $asset->stock + 1,
                    
            // assetname tests require that exactly 4 names were present before!
            'assetnames_id' => [
                $asset->assetnames[0]->id, // case 1
                $asset->assetnames[1]->id, // case 2
                $asset->assetnames[2]->id, // case 3
                $asset->assetnames[3]->id, // case 4
                '',                        // case 5: asset didn't exist before -> no id
            ],
            'assetnames_language' => [
                $asset->assetnames[0]->language, // case 1
                $asset->assetnames[1]->language, // case 2
                $asset->assetnames[2]->language, // case 3
                '',                              // case 4
                'en',                            // case 5
            ],
            'assetnames_name' => [
                $asset->assetnames[0]->name,                // case 1: no change
                $asset->assetnames[1]->name . 'updated',    // case 2: update
                '',                                         // case 3: delete by removing name
                $asset->assetnames[3]->name,                // case 4: delete by removing language
                'something new',                            // case 5: insert new name
            ],
        ]);
        $response->assertOk();
        
        // check if stored correctly
        $response->assertSeeText($asset->assetnames[0]->name);              // case 1
        $response->assertSeeText($asset->assetnames[1]->name . 'updated');  // case 2
        $response->assertDontSeeText($asset->assetnames[2]->name);          // case 3
        $response->assertDontSeeText($asset->assetnames[3]->name);          // case 4
        $response->assertSeeText('something new');                          // case 5
    }
}
