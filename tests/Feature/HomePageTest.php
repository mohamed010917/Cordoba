<?php

namespace Tests\Feature;

use App\Models\Floor;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_shows_featured_rooms_and_registration_state(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
        ]);

        $floor = Floor::query()->create([
            'name' => 'Main Floor',
            'number' => 1,
            'manger_id' => $manager->id,
        ]);

        Room::factory()
            ->count(4)
            ->create([
                'floor_id' => $floor->id,
                'manager_id' => $manager->id,
            ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Welcome')
                ->where('canRegister', true)
                ->where('availableRoomsCount', 4)
                ->has('featuredRooms', 3));
    }
}
