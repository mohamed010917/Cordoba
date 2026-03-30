<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Notifications\ClientApprovedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ApproveUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_approval_marks_the_user_as_approved_and_sends_notification(): void
    {
        Notification::fake();

        $admin = User::factory()->create([
            'name' => 'Admin User',
            'role' => 'admin',
        ]);

        $client = User::factory()->create([
            'role' => 'user',
            'is_approved' => false,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        $permission = Permission::findOrCreate('edit users', 'web');
        $admin->givePermissionTo($permission);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.users.approve', $client));

        $response->assertRedirect();

        $client->refresh();

        $this->assertSame(1, (int) $client->getRawOriginal('is_approved'));
        $this->assertSame($admin->id, $client->approved_by);
        $this->assertNotNull($client->approved_at);

        Notification::assertSentTo($client, ClientApprovedNotification::class);
    }
}
