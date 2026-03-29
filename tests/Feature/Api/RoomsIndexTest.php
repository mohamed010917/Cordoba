<?php

namespace Tests\Feature\Api;

use App\Models\Floor;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomsIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_the_rooms_api(): void
    {
        $this->getJson('/api/rooms')->assertUnauthorized();
    }

    public function test_authenticated_users_can_access_the_rooms_api(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $manager = User::factory()->create([
            'role' => 'manager',
        ]);

        $floor = Floor::factory()->create([
            'manger_id' => $manager->id,
        ]);

        $room = Room::factory()->create([
            'floor_id' => $floor->id,
            'manager_id' => $manager->id,
        ]);

        $token = $user->createToken('rooms-index-test')->plainTextToken;

        $response = $this
            ->withToken($token)
            ->getJson('/api/rooms');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.id', $room->id)
            ->assertJsonPath('data.0.number', $room->number)
            ->assertJsonPath('data.0.capacity', $room->capacity)
            ->assertJsonPath('data.0.price_cents', $room->price_cents);
    }
}
