<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PropertyMatcherTest extends TestCase
{
    /**
     * @var TestResponse
    */
    protected $properties;
    /**
     * Test Invalid ID on Match endpoint
     * @group property-matcher
     * @return void
     */
    public function test_matcher_handle_invalid_id()
    {
        #Checking invalid property id.
        $response = $this->get("/api/match/invalid-id");
        $response->assertStatus(404);
    }

    /**
     * Fetch Properties from API.
     * @return void
     */
    protected function fetchProperty() {
        if($this->properties) {
            return;
        }
        $property = Property::all()->random(1)->first();
        if(!$property) {
            $this->fail('No property available for test, please seed database.');
        }
        $response = $this->get("/api/match/{$property->uuid}");
        $this->properties = $response;
    }

    /**
     * Validate API Response
     * @group property-matcher
     * @return void
     */
    public function test_matcher_validate_response()
    {
        $this->fetchProperty();
        $this->properties->assertStatus(200);
        $properties = $this->properties->json();
        if(!isset($properties['data'])) {
            $this->fail('Matched profiles not available on response.');
        }
        if(!count($properties['data'])) {
            $this->addWarning('No matched profiles available to validate.');
        }
    }

    /**
     * Validate score for response
     * @group property-matcher
     * @return void
     */
    public function test_matcher_validate_score_order()
    {
        $this->fetchProperty();

        $properties = $this->properties->json();
        $maxMatchedCount = INF;
        $hasWrongOrder = false;
        foreach($properties['data'] as $property) {
            if($maxMatchedCount >= $property['score']) {
                $maxMatchedCount = $property['score'];
            } else {
                $hasWrongOrder = true;
            }
        }
        $this->assertNotTrue($hasWrongOrder);
    }

    /**
     * Validate if unmatched profiles are included in response.
     * @group property-matcher
     * @return void
     */
    public function test_matcher_validate_unmatched_profiles()
    {
        $this->fetchProperty();
        $properties = $this->properties->json();
        $hasUnmatchedProperties = false;
        foreach($properties['data'] as $property) {
            if($property['strictMatchesCount'] === 0 && $property['looseMatchesCount'] === 0) {
                $hasUnmatchedProperties = true;
            }
        }
        $this->assertNotTrue($hasUnmatchedProperties);
    }
}
