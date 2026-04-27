<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'event', 'auditable_type', 'auditable_id', 'old_values', 'new_values', 'url', 'ip_address', 'user_agent'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array'
    ];

    /**
     * Check if audit logging is currently enabled.
     * Reads from cache; defaults to enabled (true).
     */
    public static function isEnabled(): bool
    {
        return (bool) Cache::get('audit_logging_enabled', true);
    }

    /**
     * Enable or disable audit logging globally.
     */
    public static function setEnabled(bool $enabled): void
    {
        Cache::forever('audit_logging_enabled', $enabled);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }
}
