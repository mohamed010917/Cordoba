<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\LoginReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LoginReminderCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_reminders_to_inactive_approved_clients(): void
    {
        Notification::fake();

        $inactiveClient = User::factory()->create([
            'role' => 'user',
            'is_active' => true,
            'is_banned' => false,
            'is_approved' => true,
            'last_login_at' => now()->subDays(45),
        ]);

        $neverLoggedInClient = User::factory()->create([
            'role' => 'user',
            'is_active' => true,
            'is_banned' => false,
            'is_approved' => true,
            'last_login_at' => null,
        ]);

        $recentClient = User::factory()->create([
            'role' => 'user',
            'is_active' => true,
            'is_banned' => false,
            'is_approved' => true,
            'last_login_at' => now()->subDays(5),
        ]);

        $manager = User::factory()->create([
            'role' => 'manager',
            'is_active' => true,
            'is_banned' => false,
            'is_approved' => true,
            'last_login_at' => now()->subDays(90),
        ]);

        $bannedClient = User::factory()->create([
            'role' => 'user',
            'is_active' => true,
            'is_banned' => true,
            'is_approved' => true,
            'last_login_at' => now()->subDays(90),
        ]);

        $this->artisan('app:login-reminder', ['--days' => 30])
            ->expectsOutput('Sent 2 login reminder(s).')
            ->assertSuccessful();

        Notification::assertSentTo($inactiveClient, LoginReminderNotification::class);
        Notification::assertSentTo($neverLoggedInClient, LoginReminderNotification::class);
        Notification::assertNotSentTo($recentClient, LoginReminderNotification::class);
        Notification::assertNotSentTo($manager, LoginReminderNotification::class);
        Notification::assertNotSentTo($bannedClient, LoginReminderNotification::class);
    }
}
