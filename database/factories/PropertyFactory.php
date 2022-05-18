<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $propertyTypes = config('site.property_types');
        $randomKey = array_rand($propertyTypes);
        return [
            'uuid' => $this->faker->uuid,
            'name' => $this->faker->name,
            'address' => $this->faker->address,
            'property_type' => $propertyTypes[$randomKey]
        ];
    }
}
