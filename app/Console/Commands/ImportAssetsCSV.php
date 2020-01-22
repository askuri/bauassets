<?php

namespace App\Console\Commands;

use App\Asset;
use App\Assetname;
use App\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportAssetsCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import_assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a CSV file containing assets.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = array_map('str_getcsv', file("/home/martin/NetBeansProjects/bauassets/inventar.csv"));
        
        foreach($data as $id => $row) {
            if ($id == 0) continue; // skip header
            
            DB::transaction(function () use ($row) {
                list($location, $stock, $category, $subcat, $de1, $de2, $en1, $en2) = $row;
                $location = trim($location);
                $stock = $stock ?? null;
                $category = trim($category);

                // create category
                $category = Category::firstOrCreate(
                                ['name' => $category]
                );
                
                // create asset
                $asset = new Asset();
                $asset->location = $location;
                $asset->category_id = $category->id;
                if (!empty($stock)) $asset->stock = $stock;
                $asset->save();
                
                // create names
                if (!empty($de1)) (new Assetname(['language' => 'de', 'name' => $de1, 'asset_id' => $asset->id]))->save();
                if (!empty($de2)) (new Assetname(['language' => 'de', 'name' => $de2, 'asset_id' => $asset->id]))->save();
                if (!empty($en1)) (new Assetname(['language' => 'en', 'name' => $en1, 'asset_id' => $asset->id]))->save();
                if (!empty($en2)) (new Assetname(['language' => 'en', 'name' => $en2, 'asset_id' => $asset->id]))->save();
            });
        }
    }
}
