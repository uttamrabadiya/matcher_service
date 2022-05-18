<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\SearchProfile;

class MatcherController extends Controller
{
    public function match(Property $property)
    {
        $fields = $property->fields;
        $matchedProfiles = SearchProfile::with('fields')
            ->where('property_type', $property->property_type)
            ->where(function($query) use($fields) {
                $query->orWhereHas('fields', function($query) use($fields) {
                    foreach($fields as $field) {
                        $query->orWhere(function($query) use($field) {
                            $query->where('field', $field->field)
                                ->where(function($query) use($field) {
                                    $query->orWhereNull('loose_min_value')->orWhere('loose_min_value', '<=', $field->value);
                                })
                                ->where(function($query) use($field) {
                                    $query->orWhereNull('loose_max_value')->orWhere('loose_max_value', '>=', $field->value);
                                });
                        });
                    }
                });
            })->get();
        $response = [];
        foreach($matchedProfiles as $matchedProfile) {
            $looseMatchCount = 0;
            $strictMatchCount = 0;
            $profileFields = $matchedProfile->fields;
            foreach($profileFields as $profileField) {
                foreach($fields as $field) {
                    if($profileField->field === $field->field) {
                        if((is_null($profileField->min_value) || $field->value >= $profileField->min_value)
                           && (is_null($profileField->max_value) || $field->value <= $profileField->max_value)) {
                            $strictMatchCount++;
                            break;
                        }
                        if((is_null($profileField->loose_min_value) || $field->value >= $profileField->loose_min_value)
                            && (is_null($profileField->loose_max_value) || $field->value <= $profileField->loose_max_value)) {
                            $looseMatchCount++;
                            break;
                        }
                    }
                }
            }
            $matchCount = $strictMatchCount + $looseMatchCount;
            if(!$matchCount) {
                continue;
            }
            $response[] = [
                'searchProfileId' => $matchedProfile->uuid,
                'score' => $matchCount,
                'strictMatchesCount' => $strictMatchCount,
                'looseMatchesCount' => $looseMatchCount,
            ];
        }
        $response = collect($response)->sortByDesc('score')->values()->toArray();
        return ['data' => $response];
    }
}
