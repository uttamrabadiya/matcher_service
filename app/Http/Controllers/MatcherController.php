<?php

namespace App\Http\Controllers;

use App\Http\Resources\MatchedProfileTransformer;
use App\Models\Property;
use App\Services\PropertyService;


class MatcherController extends Controller
{
    protected $service;

    protected $transformer;

    public function __construct()
    {
        $this->service = new PropertyService();
    }

    public function match(Property $property)
    {
        $matchedProfiles = $this->service->searchProfiles($property);
        $response = [];
        foreach($matchedProfiles as $matchedProfile) {
            $response[] = (new MatchedProfileTransformer($matchedProfile))
                ->setProperty($property)
                ->toArray(request());
        }
        $response = collect($response)->sortByDesc('score')->values()->toArray();
        return ['data' => $response];
    }
}
