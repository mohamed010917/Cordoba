<x-mail::message>
# Welcome, {{ $clientName }}

Your client account for **Simple Hotel System** has been approved and is ready to use.

@if ($approvedByName)
**Approved by:** {{ $approvedByName }}
@endif

You can now sign in to manage your reservations, review booking details, and stay updated with hotel activity.

<x-mail::button :url="$loginUrl">
Sign in to your account
</x-mail::button>

If you were not expecting this update, please contact the hotel support team before signing in.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
