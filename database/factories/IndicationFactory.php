<?php

namespace Database\Factories;

use App\Models\Indication;
use Illuminate\Database\Eloquent\Factories\Factory;

class IndicationFactory extends Factory
{
    protected $model = Indication::class;

    public function definition(): array
    {
        $indications = [
            'Diabetes',
            'Hair Loss',
            'Hypertension',
            'Asthma',
            'Depression',
            'Arthritis',
            'Migraines',
            'Eczema',
            'High Cholesterol',
            'Allergies',
        ];

        return [
            'name' => $this->faker->randomElement($indications),
        ];
    }
}
