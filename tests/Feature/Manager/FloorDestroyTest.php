<?php

namespace Tests\Feature\Manager;

use App\Models\Floor;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FloorDestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_cannot_delete_floor_that_has_rooms(): void
    {
        /** @var User $manager */
        $manager = User::factory()->createOne([
            'role' => 'manager',
        ]);

        $floor = Floor::factory()->create([
            'manger_id' => $manager->id,
        ]);

        Room::factory()->create([
            'floor_id' => $floor->id,
            'manager_id' => $manager->id,
        ]);

        $this->actingAs($manager)
            ->from(route('manager.floors.index'))
            ->delete(route('manager.floors.destroy', $floor))
            ->assertRedirect(route('manager.floors.index'))
            ->assertSessionHasErrors('floor');

        $this->assertDatabaseHas('floors', [
            'id' => $floor->id,
        ]);
    }

    public function test_manager_can_delete_floor_without_rooms(): void
    {
        /** @var User $manager */
        $manager = User::factory()->createOne([
            'role' => 'manager',
        ]);

        $floor = Floor::factory()->create([
            'manger_id' => $manager->id,
        ]);

        $this->actingAs($manager)
            ->from(route('manager.floors.index'))
            ->delete(route('manager.floors.destroy', $floor))
            ->assertRedirect(route('manager.floors.index'))
            ->assertSessionHas('success', 'Floor deleted successfully.');

        $this->assertDatabaseMissing('floors', [
            'id' => $floor->id,
        ]);
    }
}
