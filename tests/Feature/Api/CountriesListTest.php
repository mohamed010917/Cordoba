<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Nnjeim\World\Models\Country;
use Tests\TestCase;

class CountriesListTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_fetch_the_public_countries_list_as_json(): void
    {
        Country::query()->insert([
            'iso2' => 'ZZ',
            'name' => 'Testland',
            'status' => 1,
            'phone_code' => '0',
            'iso3' => 'ZZZ',
            'region' => 'Test',
            'subregion' => 'Test',
        ]);

        $response = $this->getJson('/api/countries');

        $response->assertOk();
        $response->assertJsonFragment([
            'id' => 1,
            'name' => 'Testland',
        ]);
    }
}
