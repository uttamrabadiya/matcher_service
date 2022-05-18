<?php

namespace Database\Seeders;

use App\Models\SearchProfile;
use App\Models\SearchProfileField;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SearchProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        SearchProfile::truncate();
        SearchProfileField::truncate();
        Schema::enableForeignKeyConstraints();
        $propertyFields = config('site.property_fields');

        SearchProfile::factory()
            ->count(500)
            ->create()->each(function($searchProfile) use($propertyFields) {
                $faker = \Faker\Factory::create();
                $fields = [];
                $randomInsertableFields = array_rand($propertyFields);
                $randomInsertableFields = $randomInsertableFields+1;
                $randomKeys = array_rand($propertyFields, $randomInsertableFields);
                $randomKeys = !is_array($randomKeys) ? [$randomKeys] : $randomKeys;
                foreach($randomKeys as $randomKey) {
                    $propertyField = $propertyFields[$randomKey];
                    $minValue = 1;
                    $maxValue = 1;
                    if($propertyField === 'area') {
                        $minValue = $faker->numberBetween(100, 400);
                        $maxValue = $faker->numberBetween($minValue, 400);
                    }
                    if($propertyField === 'yearOfConstruction') {
                        $minValue = $faker->numberBetween(2010, now()->year);
                        $maxValue = $faker->numberBetween($minValue, now()->year);
                    }
                    if($propertyField === 'rooms') {
                        $minValue = $faker->numberBetween(1, 6);
                        $maxValue = $faker->numberBetween($minValue, 6);
                    }
                    if($propertyField === 'parking') {
                        $minValue = $faker->numberBetween(0, 1);
                        $maxValue = $faker->numberBetween($minValue, 1);
                    }
                    if($propertyField === 'returnActual') {
                        $minValue = $faker->randomFloat(2, 5, 25);
                        $maxValue = $faker->randomFloat(2, $minValue, 25);
                    }
                    if($propertyField === 'price') {
                        $minValue = $faker->numberBetween(100000,350000);
                        $maxValue = $faker->numberBetween($minValue,350000);
                    }
                    $canNullMinValue = rand(0, 1);
                    $canNullMaxValue = rand(0, 1);
                    $minValue = $canNullMinValue ? null : $minValue;
                    $maxValue = $canNullMaxValue && !is_null($minValue) ? null : $maxValue;
                    $fields[] = [
                        'field' => $propertyField,
                        'min_value' => $minValue,
                        'max_value' => $maxValue,
                    ];
                }

                $searchProfile->fields()->createMany($fields);
            });
    }
}
