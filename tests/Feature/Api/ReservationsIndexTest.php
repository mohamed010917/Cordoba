<?php

namespace Tests\Feature\Api;

use App\Models\Floor;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationsIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_the_reservations_api(): void
    {
        $this->getJson('/api/reservations')->assertUnauthorized();
    }

    public function test_authenticated_users_can_filter_sort_and_include_reservations(): void
    {
        $apiUser = User::factory()->create([
            'role' => 'user',
        ]);

        $manager = User::factory()->create([
            'role' => 'manager',
        ]);

        $client = User::factory()->create([
            'role' => 'user',
        ]);

        $otherClient = User::factory()->create([
            'role' => 'user',
        ]);

        $receptionist = User::factory()->create([
            'role' => 'receptionist',
        ]);

        $floor = Floor::factory()->create([
            'manger_id' => $manager->id,
        ]);

        $room = Room::factory()->create([
            'floor_id' => $floor->id,
            'manager_id' => $manager->id,
        ]);

        $otherRoom = Room::factory()->create([
            'floor_id' => $floor->id,
            'manager_id' => $manager->id,
        ]);

        $matchingReservation = Reservation::factory()->create([
            'client_id' => $client->id,
            'room_id' => $room->id,
            'receptionist_id' => $receptionist->id,
            'paid_price_cents' => 45000,
            'accompany_number' => 2,
            'created_at' => now()->subDay(),
        ]);

        Payment::factory()->create([
            'reservation_id' => $matchingReservation->id,
        ]);

        Reservation::factory()->create([
            'client_id' => $otherClient->id,
            'room_id' => $otherRoom->id,
            'receptionist_id' => $receptionist->id,
            'paid_price_cents' => 15000,
            'accompany_number' => 1,
            'created_at' => now(),
        ]);

        $token = $apiUser->createToken('reservations-index-test')->plainTextToken;

        $response = $this
            ->withToken($token)
            ->getJson('/api/reservations?filter[client_id]='.$client->id.'&filter[min_paid_price_cents]=40000&sort=-paid_price_cents&include=client,room.floor,payments');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $matchingReservation->id)
            ->assertJsonPath('data.0.client_id', $client->id)
            ->assertJsonPath('data.0.room.id', $room->id)
            ->assertJsonPath('data.0.room.floor.id', $floor->id)
            ->assertJsonPath('data.0.client.id', $client->id)
            ->assertJsonPath('data.0.payments.0.reservation_id', $matchingReservation->id);
    }
}
