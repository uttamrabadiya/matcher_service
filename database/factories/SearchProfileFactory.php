<?php

namespace Database\Factories;

use App\Models\SearchProfile;
use App\Models\SearchProfileField;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;

class SearchProfileFactory extends Factory
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
            'property_type' => $propertyTypes[$randomKey]
        ];
    }
}
