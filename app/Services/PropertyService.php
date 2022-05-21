<?php


namespace App\Services;


use App\Models\Property;
use App\Models\SearchProfile;

class PropertyService
{

    public function searchProfiles(Property $property)
    {
        $fields = $property->fields;
        $profiles = SearchProfile::with('fields')
            ->where('property_type', $property->property_type)
            ->whereHas('fields', function($query) use($fields) {
                $query->where(function($query) use($fields) {
                    foreach($fields as $field) {
                        $query->orWhere(function($query) use($field) {
                            $query->matchField($field->field, $field->value);
                        });
                    }
                });
            });
        return $profiles->get();
    }
}
