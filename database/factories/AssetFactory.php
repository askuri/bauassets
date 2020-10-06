<?php

namespace Database\Factories;

use App\Asset;
use App\Assetname;
use App\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Asset::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'location' => "da vorne links",
            'category_id' => Category::factory(),
            'stock' => $this->faker->numberBetween(1, 5),
        ];
    }
    
    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure() {
        return $this->afterCreating(function (Asset $asset) {
            $asset->assetnames()
                // add some names to the asset. That is always done as an asset
                // without any names is not allowed.
                ->saveMany(
                    Assetname::factory()->count(4)->create(['asset_id' => $asset->id])
                );
        });
    }

}