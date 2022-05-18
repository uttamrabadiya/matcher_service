<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyField;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Property::truncate();
        PropertyField::truncate();
        Schema::enableForeignKeyConstraints();
        $propertyFields = config('site.property_fields');
        Property::factory()
            ->count(50)
            ->create()->each(function($property) use($propertyFields) {
                $faker = \Faker\Factory::create();
                $fields = [];
                $randomInsertableFields = array_rand($propertyFields);
                $randomInsertableFields = $randomInsertableFields+1;
                $randomKeys = array_rand($propertyFields, $randomInsertableFields);
                $randomKeys = !is_array($randomKeys) ? [$randomKeys] : $randomKeys;
                foreach($randomKeys as $randomKey) {
                    $propertyField = $propertyFields[$randomKey];
                    $value = 1;
                    if($propertyField === 'area') {
                        $value = $faker->numberBetween(100, 400);
                    }
                    if($propertyField === 'yearOfConstruction') {
                        $value = $faker->numberBetween(2010, now()->year);
                    }
                    if($propertyField === 'rooms') {
                        $value = $faker->numberBetween(1, 6);
                    }
                    if($propertyField === 'parking') {
                        $value = $faker->numberBetween(0, 1);
                    }
                    if($propertyField === 'returnActual') {
                        $value = $faker->randomFloat(2, 5, 25);
                    }
                    if($propertyField === 'price') {
                        $value = $faker->numberBetween(100000,350000);
                    }
                    $fields[] = [
                        'field' => $propertyFields[$randomKey],
                        'value' => $value,
                    ];
                }

                $property->fields()->createMany($fields);
            });

    }
}
