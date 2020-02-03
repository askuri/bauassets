<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Asset;
use Faker\Generator as Faker;

// create some asset
$factory->define(Asset::class, function (Faker $faker) {
    return [
        'location' => "da vorne links",
        'category_id' => factory(App\Category::class),
        'stock' => $faker->numberBetween(1, 5),
    ];
});

// add names to it
$factory->afterCreating(Asset::class, function ($asset, $faker) {
    $asset->assetnames()
            ->saveMany(
                factory(App\Assetname::class, 4)->create(['asset_id' => $asset->id])
            );
});