<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LocationLookupTest extends TestCase
{
    use RefreshDatabase;

    public function test_countries_endpoint_returns_available_countries(): void
    {
        DB::table('countries')->insert([
            'id' => 1,
            'iso2' => 'EG',
            'name' => 'Egypt',
            'status' => 1,
            'phone_code' => '20',
            'iso3' => 'EGY',
            'region' => 'Africa',
            'subregion' => 'Northern Africa',
        ]);

        $this->getJson('/api/countries')
            ->assertOk()
            ->assertJsonFragment([
                'id' => 1,
                'name' => 'Egypt',
            ]);
    }

    public function test_cities_endpoint_returns_cities_for_the_selected_country(): void
    {
        DB::table('countries')->insert([
            'id' => 1,
            'iso2' => 'EG',
            'name' => 'Egypt',
            'status' => 1,
            'phone_code' => '20',
            'iso3' => 'EGY',
            'region' => 'Africa',
            'subregion' => 'Northern Africa',
        ]);

        DB::table('states')->insert([
            'id' => 10,
            'country_id' => 1,
            'name' => 'Cairo',
            'country_code' => 'EGY',
        ]);

        DB::table('cities')->insert([
            'id' => 100,
            'country_id' => 1,
            'state_id' => 10,
            'name' => 'Nasr City',
            'country_code' => 'EGY',
        ]);

        DB::table('cities')->insert([
            'id' => 101,
            'country_id' => 999,
            'state_id' => 10,
            'name' => 'Other City',
            'country_code' => 'ZZZ',
        ]);

        $this->getJson('/api/cities/1')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'id' => 100,
                'name' => 'Nasr City',
            ])
            ->assertJsonMissing([
                'id' => 101,
                'name' => 'Other City',
            ]);
    }
}
