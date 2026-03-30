<?php

namespace Tests\Feature;

use App\Models\Floor;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ReservationBookingTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_booking_fails_when_the_room_has_already_been_reserved(): void
    {
        $client = User::factory()->create([
            'role' => 'user',
        ]);

        $manager = User::factory()->create([
            'role' => 'manager',
        ]);

        $existingClient = User::factory()->create([
            'role' => 'user',
        ]);

        $floor = Floor::factory()->create([
            'manger_id' => $manager->id,
        ]);

        $room = Room::factory()->create([
            'floor_id' => $floor->id,
            'manager_id' => $manager->id,
        ]);

        Reservation::factory()->create([
            'client_id' => $existingClient->id,
            'room_id' => $room->id,
        ]);

        $chargeMock = Mockery::mock('alias:Stripe\\Charge');
        $chargeMock->shouldNotReceive('create');

        $response = $this
            ->actingAs($client)
            ->from(route('reservations.create', $room))
            ->post(route('reservations.store'), [
                'room_id' => $room->id,
                'accompany_number' => 1,
                'stripe_token' => 'tok_test',
            ]);

        $response
            ->assertRedirect(route('reservations.create', $room))
            ->assertSessionHasErrors('room_id');

        $this->assertSame(1, Reservation::query()->where('room_id', $room->id)->count());
    }
}
