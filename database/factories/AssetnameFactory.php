<?php

namespace Database\Factories;

use App\Assetname;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetnameFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Assetname::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'language' => $this->faker->languageCode,
            'name' => $this->faker->text(30),
        ];
    }
}