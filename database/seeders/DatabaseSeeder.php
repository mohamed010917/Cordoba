<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

     
        $admin = Role::create(['name' => 'admin']);
        $manager = Role::create(['name' => 'manager']);
        $receptionist = Role::create(['name' => 'user']);


        // premsions

     
        // User::factory(10)->create();

        $admin = User::factory()->create([
            'name' => 'admin ',
            'email' => 'admin@admin.admin',
            'role' => 'admin',
            
        ]);
        $manager = User::factory()->create([
            'name' => 'manager ',
            'email' => 'manager@manager.manager',
            'role' => 'manager',
        ]);
        $user = User::factory()->create([
            'name' => 'user ',
            'email' => 'user@user.user',    
            'role' => 'user',
        ]);

        $admin->assignRole("admin");
        $manager->assignRole("manager");
        $user->assignRole("user");


        User::factory(30)->create();
        $this->call([
            CountrieSeeder::class,
            FloorSeeder::class,
            RoomSeeder::class,
            ReservationSeeder::class,
            PaymentSeeder::class,
            ApprovalSeeder::class,
        ]);

    }
}
