<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Loan;
use Faker\Generator as Faker;

$factory->define(Loan::class, function (Faker $faker) {
    return [
        'date_given' => now(),
        'date_returned' => now(),
        'borrower_name' => $faker->text(20),
        'borrower_room' => 104, // LoanTests, testUpdateLoanAuthorized increases this by one!
        'comment' => 'kjahsdfk',
        'borrower_email' => "e@e",
    ];
});

// a loan is considered immutable if it has been handed out
$factory->state(Loan::class, 'not_immutable', [
    'date_given' => null,
    'date_returned' => null,
]);

$factory->afterCreatingState(Loan::class, 'with_assets', function ($loan, $faker) {
    $loan->assets()
            ->saveMany(
                factory(App\Asset::class, 3)->create()
            );
});
