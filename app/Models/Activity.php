<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'logic',
        'next_step',
        'start_date',
        'due_date',
        'lead_id',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
    ];

    /**
     * Status constants for clarity.
     */
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Human-readable status labels.
     */
    public const STATUS_LABELS = [
        self::STATUS_IN_PROGRESS => 'In Progress',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    /**
     * The "booted" method of the model.
     * Automatically scopes queries to current user and sets user_id on create.
     */
    protected static function booted(): void
    {
        // Global scope: only show activities belonging to the authenticated user
        static::addGlobalScope('user', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('activities.user_id', auth()->id());
            }
        });

        // Auto-set user_id when creating
        static::creating(function (Activity $activity) {
            if (auth()->check() && !$activity->user_id) {
                $activity->user_id = auth()->id();
            }
        });
    }

    /**
     * Get human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Calculate days until due date.
     * Positive = days remaining, Negative = days overdue, Null = no due date
     */
    public function getDaysUntilDueAttribute(): ?int
    {
        if (!$this->due_date) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->due_date, false);
    }

    /**
     * Check if activity is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        if (!$this->due_date) {
            return false;
        }

        if (in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED])) {
            return false;
        }

        return $this->due_date->isPast();
    }

    /**
     * Get urgency level based on due date.
     * Returns: 'overdue', 'urgent' (0-2 days), 'soon' (3-7 days), 'normal', or null
     */
    public function getUrgencyLevelAttribute(): ?string
    {
        $days = $this->days_until_due;

        if ($days === null) {
            return null;
        }

        if ($days < 0) {
            return 'overdue';
        }

        if ($days <= 2) {
            return 'urgent';
        }

        if ($days <= 7) {
            return 'soon';
        }

        return 'normal';
    }

    /**
     * Get urgency badge HTML for display.
     */
    public function getUrgencyBadgeAttribute(): string
    {
        $level = $this->urgency_level;
        $days = $this->days_until_due;

        if ($level === null) {
            return '<span class="badge badge-neutral">No due date</span>';
        }

        $text = match ($level) {
            'overdue' => abs($days) . ' day' . (abs($days) !== 1 ? 's' : '') . ' overdue',
            'urgent' => $days === 0 ? 'Due today' : $days . ' day' . ($days !== 1 ? 's' : '') . ' left',
            'soon' => $days . ' days left',
            default => $days . ' days left',
        };

        return '<span class="badge badge-' . $level . '">' . $text . '</span>';
    }

    /**
     * Get the user that owns this activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lead person for this activity.
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'lead_id')->withoutGlobalScope('user');
    }

    /**
     * Get the participants (parties) for this activity.
     */
    public function parties(): BelongsToMany
    {
        return $this->belongsToMany(Person::class)->withoutGlobalScope('user');
    }

    /**
     * Get all people involved (lead + parties).
     */
    public function getAllPeopleAttribute()
    {
        $people = $this->parties;

        if ($this->lead) {
            $people = $people->prepend($this->lead);
        }

        return $people->unique('id');
    }

    /**
     * Scope: Overdue activities.
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('due_date', '<', now()->startOfDay())
                     ->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    /**
     * Scope: Due within next N days.
     */
    public function scopeDueSoon(Builder $query, int $days = 7): Builder
    {
        return $query->whereBetween('due_date', [now()->startOfDay(), now()->addDays($days)->endOfDay()])
                     ->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    /**
     * Scope: In progress activities.
     */
    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope: Completed activities.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope: Active (not completed or cancelled).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    /**
     * Scope: Search by name.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('name', 'like', "%{$term}%");
    }

    /**
     * Scope: Order by urgency (overdue first, then by due date).
     */
    public function scopeOrderByUrgency(Builder $query): Builder
    {
        return $query->orderByRaw('CASE WHEN due_date < CURDATE() THEN 0 ELSE 1 END')
                     ->orderBy('due_date');
    }
}
