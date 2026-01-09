<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'daily_summary_enabled',
        'daily_summary_time',
        'email_notifications',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'daily_summary_enabled' => 'boolean',
        'email_notifications' => 'boolean',
        'daily_summary_time' => 'datetime:H:i',
    ];

    /**
     * Get the user that owns these settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
