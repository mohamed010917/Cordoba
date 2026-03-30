<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\LoginReminderNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:login-reminder {--days=30 : Days since the last login before sending a reminder}')]
#[Description('Send login reminder emails to inactive client users')]
class LoginReminder extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $inactiveDays = max(1, (int) $this->option('days'));
        $cutoff = now()->subDays($inactiveDays);
        $remindedCount = 0;

        User::query()
            ->where('role', 'user')
            ->where('is_active', true)
            ->where('is_banned', false)
            ->where('is_approved', true)
            ->where(function ($query) use ($cutoff): void {
                $query->whereNull('last_login_at')
                    ->orWhere('last_login_at', '<=', $cutoff);
            })
            ->orderBy('id')
            ->chunkById(100, function ($users) use ($inactiveDays, &$remindedCount): void {
                foreach ($users as $user) {
                    $user->notify(new LoginReminderNotification($inactiveDays));
                    $remindedCount++;
                }
            });

        $this->info("Sent {$remindedCount} login reminder(s).");

        return self::SUCCESS;
    }
}
