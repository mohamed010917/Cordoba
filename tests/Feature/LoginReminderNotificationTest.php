<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\LoginReminderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LoginReminderNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_be_sent_to_an_inactive_client(): void
    {
        Notification::fake();

        $client = User::factory()->create([
            'role' => 'user',
        ]);

        $client->notify(new LoginReminderNotification(30));

        Notification::assertSentTo($client, LoginReminderNotification::class);
    }

    public function test_it_is_queued_and_uses_the_mail_channel(): void
    {
        $client = User::factory()->create([
            'role' => 'user',
        ]);

        $notification = new LoginReminderNotification(30);

        $this->assertInstanceOf(ShouldQueue::class, $notification);
        $this->assertSame(['mail'], $notification->via($client));
        $this->assertSame(['inactive_days' => 30], $notification->toArray($client));
    }

    public function test_it_builds_the_expected_mail_message(): void
    {
        $client = User::factory()->create([
            'name' => 'Jane Client',
            'role' => 'user',
        ]);

        $notification = new LoginReminderNotification(30);
        $mailMessage = $notification->toMail($client);

        $this->assertSame('We miss you at Simple Hotel System', $mailMessage->subject);
        $this->assertSame('hotel', $mailMessage->theme);
        $this->assertSame('emails.login-reminder', $mailMessage->markdown);
        $this->assertSame('Jane Client', $mailMessage->viewData['clientName']);
        $this->assertSame(30, $mailMessage->viewData['inactiveDays']);
        $this->assertSame(route('login'), $mailMessage->viewData['loginUrl']);
    }

    public function test_it_renders_the_custom_mail_template(): void
    {
        $client = User::factory()->create([
            'name' => 'Jane Client',
            'role' => 'user',
        ]);

        $notification = new LoginReminderNotification(30);
        $renderedMail = $notification->toMail($client)->render()->toHtml();

        $this->assertStringContainsString('We miss you, Jane Client', $renderedMail);
        $this->assertStringContainsString('Simple Hotel System', $renderedMail);
        $this->assertStringContainsString('Guest Services Desk', $renderedMail);
        $this->assertStringContainsString('30 days', $renderedMail);
        $this->assertStringContainsString('Return to your account', $renderedMail);
    }
}
