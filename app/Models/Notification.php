<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope a query to only include read notifications.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope a query to get recent notifications.
     */
    public function scopeRecent($query, int $limit = 10)
    {
        return $query->latest()->limit($limit);
    }

    /**
     * Scope a query to filter by user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead(): bool
    {
        if ($this->read_at !== null) {
            return false; // Already read
        }

        $this->read_at = now();
        return $this->save();
    }

    /**
     * Check if notification is unread.
     */
    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    /**
     * Get the URL for the notification based on type and data.
     */
    public function getUrlAttribute(): ?string
    {
        if (empty($this->data) || !isset($this->data['application_id']) || !isset($this->data['permit_type'])) {
            return null;
        }

        $applicationId = $this->data['application_id'];
        $permitType = $this->data['permit_type'];

        // Determine the route based on user role and permit type
        $user = $this->user;
        if (!$user) {
            return null;
        }

        $routePrefix = "ex-{$permitType}.";

        // Map admin types to route segments
        $roleRouteMap = [
            'data_entry_operator' => 'operator',
            'office_assistant' => 'office-assistant',
            'secretary' => 'secretary',
            'chairman' => 'chairman',
            'super_admin' => 'admin',
        ];

        $roleSegment = $roleRouteMap[$user->admin_type] ?? 'operator';
        $routeName = $routePrefix . $roleSegment . '.show';

        try {
            return route($routeName, $applicationId);
        } catch (\Exception $e) {
            return null;
        }
    }
}
