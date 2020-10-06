<?php

namespace Database\Factories;

use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'role' => 'guest', // guests have no real ability right now. add some factory state
            'remember_token' => Str::random(10),
        ];
    }
    
    /**
     * Indicate that the user is a moderator.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function role_moderator() {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'moderator',
            ];
        });
    }

}