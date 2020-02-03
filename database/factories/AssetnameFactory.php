<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Assetname;
use Faker\Generator as Faker;

// create an assetname
$factory->define(Assetname::class, function (Faker $faker) {
    return [
        'language' => $faker->languageCode,
        'name' => $faker->text(30),
    ];
});