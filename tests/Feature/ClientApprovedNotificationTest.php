<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ClientApprovedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ClientApprovedNotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the notification can be sent to an approved client.
     */
    public function test_it_can_be_sent_to_a_client(): void
    {
        Notification::fake();

        $client = User::factory()->create([
            'role' => 'user',
        ]);

        $client->notify(new ClientApprovedNotification('Front Desk Admin'));

        Notification::assertSentTo($client, ClientApprovedNotification::class);
    }

    public function test_it_is_queued_and_uses_the_mail_channel(): void
    {
        $client = User::factory()->create([
            'role' => 'user',
        ]);

        $notification = new ClientApprovedNotification('Front Desk Admin');

        $this->assertInstanceOf(ShouldQueue::class, $notification);
        $this->assertSame(['mail'], $notification->via($client));
    }

    public function test_it_builds_the_expected_mail_message(): void
    {
        $client = User::factory()->create([
            'name' => 'Jane Client',
            'role' => 'user',
        ]);

        $notification = new ClientApprovedNotification('Front Desk Admin');
        $mailMessage = $notification->toMail($client);

        $this->assertSame('Your client account has been approved', $mailMessage->subject);
        $this->assertSame('Hello Jane Client,', $mailMessage->greeting);
        $this->assertContains('Your hotel system client account has been approved.', $mailMessage->introLines);
        $this->assertContains('Approved by: Front Desk Admin.', $mailMessage->introLines);
        $this->assertSame('Sign in to your account', $mailMessage->actionText);
    }
}
