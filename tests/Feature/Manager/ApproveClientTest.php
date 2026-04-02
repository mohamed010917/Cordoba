<?php

namespace Tests\Feature\Manager;

use App\Models\User;
use App\Notifications\ClientApprovedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ApproveClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_approval_marks_the_client_as_approved_and_sends_notification(): void
    {
        Notification::fake();

        $manager = User::factory()->create([
            'name' => 'Manager User',
            'role' => 'manager',
        ]);

        $client = User::factory()->create([
            'role' => 'user',
            'is_approved' => false,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        $permission = Permission::findOrCreate('edit users', 'web');
        $manager->givePermissionTo($permission);

        $response = $this
            ->actingAs($manager)
            ->patch(route('manager.clients.approve', $client));

        $response->assertRedirect(route('manager.clients.index'));

        $client->refresh();

        $this->assertTrue($client->is_approved);
        $this->assertSame($manager->id, $client->approved_by);
        $this->assertNotNull($client->approved_at);

        Notification::assertSentTo($client, ClientApprovedNotification::class);
    }
}
