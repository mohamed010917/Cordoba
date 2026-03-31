<x-mail::message>
# We miss you, {{ $clientName }}

It looks like you have not signed in to your **Simple Hotel System** client account for **{{ $inactiveDays }} days**.

Take a quick look at your reservations, account details, and any recent updates waiting for you.

<x-mail::button :url="$loginUrl">
Return to your account
</x-mail::button>

If you no longer need access, you can safely ignore this reminder.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
