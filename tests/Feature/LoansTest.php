<?php

namespace Tests\Feature;

use App\Loan;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoansTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    
    private $loan_noasset;
    private $loan_withassets;
    
    public function setUp(): void {
        parent::setUp();
        
        $this->loan_noasset = factory(Loan::class)->create();
        $this->loan_withassets = factory(Loan::class)->state("with_assets")->create();
    }
    
    /**
     * Test if the index displays loans that were just added
     *
     * @return void
     */
    public function testIndex()
    {
        //$this->withoutExceptionHandling();
        $response = $this->get(route('loans.index'));

        $response->assertSuccessful();
        
        $response->assertSee($this->loan_noasset->borrower_name);
        $response->assertSee($this->loan_noasset->borrower_room);
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
        foreach ($this->loan_withassets->assets as $asset) {
            foreach ($asset->assetnames as $assetname) {
                $response->assertSee($assetname->name);
            }
        }
    }
}
