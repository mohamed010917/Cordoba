<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Floor extends Model
{
    /** @use HasFactory<\Database\Factories\FloorFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'number',
        'manger_id',
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manger_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function hasRooms(): bool
    {
        return $this->rooms()->exists();
    }

    public static function generateNumber(): int
    {
        $last = static::max('number') ?? 999;

        return max((int) $last + 1, 1000);
    }
}