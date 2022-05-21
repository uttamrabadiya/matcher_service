<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyField;
use App\Models\SearchProfile;
use App\Models\SearchProfileField;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PropertySeeder extends Seeder
{
    protected function truncatePreviousData()
    {
        Schema::disableForeignKeyConstraints();
        Property::truncate();
        PropertyField::truncate();
        Schema::enableForeignKeyConstraints();
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncatePreviousData();
        $propertyFields = config('site.property_fields');
        Property::factory()
            ->count(50)
            ->create()->each(function($property) use($propertyFields) {
                $fields = [];
                $randomInsertableFields = array_rand($propertyFields);
                $randomInsertableFields = $randomInsertableFields+1;
                $randomKeys = array_rand($propertyFields, $randomInsertableFields);
                $randomKeys = !is_array($randomKeys) ? [$randomKeys] : $randomKeys;
                foreach($randomKeys as $randomKey) {
                    $propertyField = $propertyFields[$randomKey];
                    $value = $this->getValueByField($propertyField);

                    $fields[] = [
                        'field' => $propertyFields[$randomKey],
                        'value' => $value,
                    ];
                }
                $property->fields()->createMany($fields);
            });

    }

    public function getValueByField($propertyField)
    {
        $faker = \Faker\Factory::create();

        switch($propertyField) {
            case 'area':
                $value = $faker->numberBetween(100, 400);
                break;
            case 'yearOfConstruction':
                $value = $faker->numberBetween(2010, now()->year);
                break;
            case 'rooms':
                $value = $faker->numberBetween(1, 6);
                break;
            case 'parking':
                $value = $faker->numberBetween(0, 1);
                break;
            case 'returnActual':
                $value = $faker->randomFloat(2, 5, 25);
                break;
            case 'price':
                $value = $faker->numberBetween(100000,350000);
                break;
            default:
                $value = 1;
                break;
        }
        return $value;
    }
}
