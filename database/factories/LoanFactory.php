<?php

namespace Database\Factories;

use App\Loan;
use App\User;
use App\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Loan::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date_given' => now(),
            'date_returned' => now(),
            'borrower_name' => $this->faker->text(20),
            'borrower_room' => 104, // LoanTests, testUpdateLoanAuthorized increases this by one!
            'comment' => 'kjahsdfk',
            'borrower_email' => "e@e",
        ];
    }
    
    /**
     * Indicate that the loan is not immutable. That means, changes can be made.
     * A loan is immutable if the assets were already given to the borrower.
     * 
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function not_immutable() {
        return $this
            ->state(function (array $attributes) {
                return [
                    'date_given' => null,
                    'date_returned' => null,
                ];
            });
    }
    
    /**
     * Declare, that the loan must have some assets attached to it
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function with_assets() {
        return $this
            // replaces afterCreatingState from pre Laravel 8.
            // See https://stefanzweifel.io/posts/2020/09/17/nested-states-in-laravel-8-database-factories/
            // doing it like this was not in the official documentation
            ->afterCreating(function (Loan $loan) {
                $loan->assets()
                    ->saveMany(
                            Asset::factory()->count(3)->create()
                    );
            });
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure() {
        return $this->afterMaking(function (Loan $loan) {
            // add a user (issuer) to the loan
            $loan->issuer()->associate(User::factory()->role_moderator()->create());
        });
    }

}