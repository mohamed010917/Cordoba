<?php

namespace Tests\Feature\Receptionist;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_receptionist_dashboard(): void
    {
        $this->get('/receptionist/dashboard')->assertRedirect();
    }

    public function test_receptionists_can_view_the_receptionist_dashboard(): void
    {
        $receptionist = User::factory()->create([
            'role' => 'receptionist',
        ]);

        $this->actingAs($receptionist)
            ->get('/receptionist/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('receptionist/Dashboard')
                ->has('stats')
                ->has('recent_pending')
                ->has('recent_approved')
                ->has('recent_reservations'));
    }
}
