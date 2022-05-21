<?php

namespace App\Http\Resources;

use App\Models\Property;
use App\Models\PropertyField;
use App\Models\SearchProfileField;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchedProfileTransformer extends JsonResource
{
    protected $property;

    /**
     * Set property for transformer
     *
     * @param Property $property
     * @return MatchedProfileTransformer
     */
    public function setProperty($property)
    {
        $this->property = $property;
        return $this;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $looseMatchCount = 0;
        $strictMatchCount = 0;
        $profileFields = $this->fields;
        $propertyFields = collect($this->property->fields)->keyBy('field');
        foreach($profileFields as $profileField) {
            if(empty($propertyFields[$profileField->field])) {
                continue;
            }
            $propertyField = $propertyFields[$profileField->field];
            $isStrictMatched = $this->profileStrictMatch($profileField, $propertyField);
            if($isStrictMatched) {
                $strictMatchCount++;
                continue;
            }
            $isLooseMatched = $this->profileLooseMatch($profileField, $propertyField);
            if($isLooseMatched) {
                $looseMatchCount++;
            }
        }

        return [
            'searchProfileId' => $this->uuid,
            'score' => ($strictMatchCount + $looseMatchCount),
            'strictMatchesCount' => $strictMatchCount,
            'looseMatchesCount' => $looseMatchCount,
        ];
    }

    /**
     * Strict Match Profile with given property
     *
     * @param SearchProfileField $profileField
     * @param PropertyField $propertyField
     * @return boolean
     */
    protected function profileStrictMatch($profileField, $propertyField) {
        if((is_null($profileField->min_value) || $propertyField->value >= $profileField->min_value)
            && (is_null($profileField->max_value) || $propertyField->value <= $profileField->max_value)) {
            return 1;
        }
        return 0;
    }

    /**
     * Loose Match Profile with given property
     *
     * @param SearchProfileField $profileField
     * @param PropertyField $propertyField
     * @return boolean
     */
    protected function profileLooseMatch($profileField, $propertyField) {
        if((is_null($profileField->loose_min_value) || $propertyField->value >= $profileField->loose_min_value)
            && (is_null($profileField->loose_max_value) || $propertyField->value <= $profileField->loose_max_value)) {
            return 1;
        }
        return 0;
    }
}
