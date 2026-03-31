<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_approved_users_can_visit_the_dashboard()
    {
        $user = User::factory()->create([
            'is_approved' => true,
            'approved_at' => now(),
        ]);
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_pending_users_are_redirected_to_the_pending_approval_page(): void
    {
        $user = User::factory()->create([
            'is_approved' => false,
            'approved_at' => null,
        ]);

        $this->actingAs($user);

        $this->get(route('dashboard'))
            ->assertRedirect(route('pending-approval'));
    }

    public function test_pending_users_cannot_access_client_booking_routes(): void
    {
        $user = User::factory()->create([
            'is_approved' => false,
            'approved_at' => null,
        ]);

        $this->actingAs($user);

        $this->get(route('rooms.index'))
            ->assertRedirect(route('pending-approval'));
    }
}
