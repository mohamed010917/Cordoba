<?php

namespace Tests\Feature\Receptionist;

use App\Models\User;
use App\Notifications\ClientApprovedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ApproveClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_receptionist_approval_marks_the_client_as_approved_and_sends_notification(): void
    {
        Notification::fake();

        $receptionist = User::factory()->create([
            'name' => 'Receptionist User',
            'role' => 'receptionist',
        ]);

        $client = User::factory()->create([
            'role' => 'user',
            'is_approved' => false,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        $response = $this
            ->actingAs($receptionist)
            ->patch(route('receptionist.clients.approve', $client));

        $response->assertRedirect(route('receptionist.clients.pending'));

        $client->refresh();

        $this->assertTrue($client->is_approved);
        $this->assertSame($receptionist->id, $client->approved_by);
        $this->assertNotNull($client->approved_at);

        Notification::assertSentTo($client, ClientApprovedNotification::class);
    }
}
