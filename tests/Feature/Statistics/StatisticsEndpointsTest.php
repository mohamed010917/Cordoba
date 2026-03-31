<?php

namespace Tests\Feature\Statistics;

use App\Models\Floor;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatisticsEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_access_all_statistics_json_endpoints(): void
    {
        [$manager] = $this->prepareBaseData();

        $this->actingAs($manager);

        $this->get(route('manager.statistics.gender'))
            ->assertOk()
            ->assertJsonStructure(['labels', 'data']);

        $this->get(route('manager.statistics.revenue'))
            ->assertOk()
            ->assertJsonStructure(['labels', 'data']);

        $this->get(route('manager.statistics.countries'))
            ->assertOk()
            ->assertJsonStructure(['labels', 'data']);

        $this->get(route('manager.statistics.top-clients'))
            ->assertOk()
            ->assertJsonStructure(['labels', 'data']);
    }

    public function test_revenue_response_has_12_months(): void
    {
        [$manager] = $this->prepareBaseData();

        $this->actingAs($manager);

        $payload = $this->get(route('manager.statistics.revenue'))
            ->assertOk()
            ->json();

        $this->assertCount(12, $payload['labels']);
        $this->assertCount(12, $payload['data']);
    }

    public function test_year_filter_changes_the_result_set(): void
    {
        [$manager, $room, $client] = $this->prepareBaseData();

        $reservation = Reservation::query()->create([
            'client_id' => $client->id,
            'room_id' => $room->id,
            'accompany_number' => 1,
            'paid_price_cents' => 25000,
            'receptionist_id' => null,
        ]);

        $reservation->created_at = now()->subYear();
        $reservation->updated_at = now()->subYear();
        $reservation->save();

        $this->actingAs($manager);

        $payload = $this->get('/manager/statistics/gender?filter[year]='.now()->year)
            ->assertOk()
            ->json();

        $this->assertSame(0, array_sum($payload['data']));
    }

    public function test_top_clients_returns_maximum_ten_clients(): void
    {
        [$manager, $room] = $this->prepareBaseData();

        foreach (range(1, 15) as $index) {
            $client = User::factory()->create([
                'role' => 'user',
                'name' => "Client {$index}",
            ]);

            Reservation::query()->create([
                'client_id' => $client->id,
                'room_id' => $room->id,
                'accompany_number' => 1,
                'paid_price_cents' => 20000 + $index,
                'receptionist_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->actingAs($manager);

        $payload = $this->get(route('manager.statistics.top-clients'))
            ->assertOk()
            ->json();

        $this->assertCount(10, $payload['labels']);
        $this->assertCount(10, $payload['data']);
    }

    /**
     * @return array{0: User, 1: Room, 2: User}
     */
    private function prepareBaseData(): array
    {
        $manager = User::factory()->create(['role' => 'manager']);

        $floor = Floor::query()->create([
            'name' => 'First Floor',
            'number' => 1,
            'manger_id' => $manager->id,
        ]);

        $room = Room::query()->create([
            'number' => 'Room-101',
            'capacity' => 2,
            'price_cents' => 10000,
            'floor_id' => $floor->id,
            'manager_id' => $manager->id,
        ]);

        $client = User::factory()->create([
            'role' => 'user',
            'gender' => 'male',
        ]);

        return [$manager, $room, $client];
    }
}
