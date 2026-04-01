<?php

namespace Tests\Feature\Manager;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_manager_dashboard(): void
    {
        $this->get('/manager/dashboard')->assertRedirect();
    }

    public function test_managers_can_view_the_manager_dashboard(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
        ]);

        $this->actingAs($manager)
            ->get('/manager/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('manager/Dashboard')
                ->has('stats')
                ->has('latest_reservations')
                ->has('latest_clients')
                ->has('reservation_trend'));
    }
}
