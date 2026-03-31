<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (DB::table('countries')->count() === 0) {
            $this->call(WorldSeeder::class);
        }

        $admin = Role::create(['name' => 'admin']);
        $manager = Role::create(['name' => 'manager']);
        $client = Role::create(['name' => 'user']);
        $receptionist = Role::create(['name' => 'receptionist']);

        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);
        Permission::create(['name' => 'view users']);
        $admin->givePermissionTo(Permission::all());
        $manager->givePermissionTo(['view users', 'edit users', 'create users', 'delete users']);

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

        $receptionist = User::factory()->create([
            'name' => 'receptionist ',
            'email' => 'rec@rec.rec',
            'role' => 'receptionist',
        ]);

        $admin->assignRole('admin');
        $manager->assignRole('manager');
        $user->assignRole('user');
        $receptionist->assignRole('receptionist');

        User::factory(30)->create()->each(function (User $user): void {
            $user->assignRole('user');
        });

        $this->call([
            FloorSeeder::class,
            RoomSeeder::class,
            ReservationSeeder::class,
            PaymentSeeder::class,
            ApprovalSeeder::class,
        ]);
    }
}
