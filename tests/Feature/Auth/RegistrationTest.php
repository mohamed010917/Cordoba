<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Fortify\Features;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->skipUnlessFortifyFeature(Features::registration());
    }

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get(route('register'));

        $response->assertOk();
    }

    public function test_new_users_can_register()
    {
        $countryId = DB::table('countries')->insertGetId([
            'iso2' => 'EG',
            'name' => 'Egypt',
            'status' => 1,
            'phone_code' => '20',
            'iso3' => 'EGY',
            'region' => 'Africa',
            'subregion' => 'Northern Africa',
        ]);

        $stateId = DB::table('states')->insertGetId([
            'country_id' => $countryId,
            'name' => 'Cairo',
            'country_code' => 'EGY',
        ]);

        $cityId = DB::table('cities')->insertGetId([
            'country_id' => $countryId,
            'state_id' => $stateId,
            'name' => 'Nasr City',
            'country_code' => 'EGY',
        ]);

        $response = $this->post(route('register.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'gender' => 'male',
            'country_id' => $countryId,
            'city_id' => $cityId,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('pending-approval', absolute: false));
    }
}
