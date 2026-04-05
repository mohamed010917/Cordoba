<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Nnjeim\World\Models\City;
use Nnjeim\World\Models\Country;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'image', 'role', 'is_active', 'is_banned', 'phone', 'national_id', 'created_by_manager_id', 'gender', 'is_approved', 'approved_at', 'approved_by', 'banned_at', 'banned_by', 'country_id', 'city_id', 'last_login_at'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens;

    use HasFactory;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'banned_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'is_active' => 'boolean',
            'is_banned' => 'boolean',
            'is_approved' => 'boolean',
        ];
    }

    public function createdByManager()
    {
        return $this->belongsTo(User::class, 'created_by_manager_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function bannedBy()
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function createdReceptionists()
    {
        return $this->hasMany(User::class, 'created_by_manager_id');
    }

    public function approvedClients()
    {
        return $this->hasMany(User::class, 'approved_by');
    }

    public function clientReservations()
    {
        return $this->hasMany(Reservation::class, 'client_id');
    }

    public function receptionistReservations()
    {
        return $this->hasMany(Reservation::class, 'receptionist_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isReceptionist(): bool
    {
        return $this->role === 'receptionist';
    }

    public function isClient(): bool
    {
        return $this->role === 'user';
    }

    public function isApproved(): bool
    {
        return $this->approved_at !== null;
    }

    public function isPending(): bool
    {
        return $this->approved_at === null;
    }

    public function isBanned(): bool
    {
        return (bool) $this->is_banned;
    }

    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    // scopes
    public function scopeReceptionists(Builder $query): Builder
    {
        return $query->where('role', 'receptionist');
    }

    public function scopeClients(Builder $query): Builder
    {
        return $query->where('role', 'user');
    }

    public function scopeManagers(Builder $query): Builder
    {
        return $query->where('role', 'manager');
    }

    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('role', 'admin');
    }

    public function scopePendingClients(Builder $query): Builder
    {
        return $query->where('role', 'user')->whereNull('approved_at');
    }

    public function scopeApprovedClients(Builder $query): Builder
    {
        return $query->where('role', 'user')->whereNotNull('approved_at');
    }

    public function scopeBannedClients(Builder $query): Builder
    {
        return $query->where('role', 'user')->whereNotNull('banned_at');
    }

    public function scopeActiveClients(Builder $query): Builder
    {
        return $query->where('role', 'user')->where('is_active', true);
    }

    public function scopeBannedReceptionists(Builder $query): Builder
    {
        return $query->where('role', 'receptionist')->whereNotNull('banned_at');
    }

    public function scopeActiveReceptionists(Builder $query): Builder
    {
        return $query->where('role', 'receptionist')->where('is_active', true);
    }

    public function scopeBannedManagers(Builder $query): Builder
    {
        return $query->where('role', 'manager')->whereNotNull('banned_at');
    }

    public function scopeActiveManagers(Builder $query): Builder
    {
        return $query->where('role', 'manager')->where('is_active', true);
    }

    public function reservation() : HasMany
    {
        return $this->hasMany(Reservation::class, 'client_id');
    }
}
