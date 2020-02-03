<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Loan;
use Faker\Generator as Faker;

$factory->define(Loan::class, function (Faker $faker) {
    return [
        'date_given' => now(),
        'date_returned' => now(),
        'borrower_name' => $faker->name,
        'borrower_room' => 104,
        'comment' => 'kjahsdfk',
        'borrower_email' => "e@e",
    ];
});

$factory->state(App\User::class, 'with_assets', [
    
]);

$factory->afterCreatingState(Loan::class, 'with_assets', function ($loan, $faker) {
    $loan->assets()
            ->saveMany(
                factory(App\Asset::class, 3)->create()
            );
});
