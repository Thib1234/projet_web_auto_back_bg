<?php

namespace Database\Factories;

use App\Models\Ad;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdFactory extends Factory
{
    protected $model = Ad::class;

    public function definition()
    {
        $brands = ['Toyota', 'Honda', 'BMW', 'Mercedes', 'Audi', 'Ford', 'Chevrolet', 'Renault', 'Peugeot', 'Citroën'];
        $fuelTypes = ['Essence', 'Diesel', 'Hybride', 'Électrique'];
        $transmissions = ['Manuelle', 'Automatique'];
        
        return [
            'brand' => $this->faker->randomElement($brands),
            'model' => $this->faker->word,
            'year' => $this->faker->numberBetween(2000, 2025),
            'mileage' => $this->faker->numberBetween(5000, 200000),
            'price' => $this->faker->numberBetween(1000, 50000),
            'fuel_type' => $this->faker->randomElement($fuelTypes),
            'transmission' => $this->faker->randomElement($transmissions),
            'description' => $this->faker->paragraphs(3, true),
            'status' => 'available',
            'user_id' => User::factory(),
        ];
    }
}