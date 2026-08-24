<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notification extends Model
{
    use HasUuid;

    protected $table = 'notifications';

    protected $fillable = [
        'type',
        'title',
        'message',
        'action_url',
        'action_label',
        'icon',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function recipients(): HasMany
    {
        return $this->hasMany(NotificationRecipient::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('recipients', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function isReadBy($userId): bool
    {
        return $this->recipients()
            ->where('user_id', $userId)
            ->whereNotNull('read_at')
            ->exists();
    }

    public function markAsReadBy($userId): void
    {
        $this->recipients()
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function markAllAsReadBy($userId): void
    {
        NotificationRecipient::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
