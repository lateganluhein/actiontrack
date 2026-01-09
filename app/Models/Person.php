<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email_primary',
        'email_secondary',
        'phone_primary',
        'phone_secondary',
        'company',
    ];

    /**
     * The "booted" method of the model.
     * Automatically scopes queries to current user and sets user_id on create.
     */
    protected static function booted(): void
    {
        // Global scope: only show people belonging to the authenticated user
        static::addGlobalScope('user', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('people.user_id', auth()->id());
            }
        });

        // Auto-set user_id when creating
        static::creating(function (Person $person) {
            if (auth()->check() && !$person->user_id) {
                $person->user_id = auth()->id();
            }
        });
    }

    /**
     * Get the full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Get the user that owns this person.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get activities where this person is the lead.
     */
    public function activitiesAsLead(): HasMany
    {
        return $this->hasMany(Activity::class, 'lead_id');
    }

    /**
     * Get activities where this person is a participant.
     */
    public function activitiesAsParty(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class);
    }

    /**
     * Get all active activities (as lead or party).
     * Excludes completed and cancelled activities.
     */
    public function getAllActivities()
    {
        $asLead = $this->activitiesAsLead()->active()->pluck('id');
        $asParty = $this->activitiesAsParty()->active()->pluck('id');

        return Activity::whereIn('id', $asLead->merge($asParty)->unique())
            ->active()
            ->orderBy('due_date')
            ->get();
    }

    /**
     * Scope to search by name or email.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
              ->orWhere('last_name', 'like', "%{$term}%")
              ->orWhere('email_primary', 'like', "%{$term}%")
              ->orWhere('company', 'like', "%{$term}%");
        });
    }

    /**
     * Scope to only people with email addresses.
     */
    public function scopeWithEmail(Builder $query): Builder
    {
        return $query->whereNotNull('email_primary')
                     ->where('email_primary', '!=', '');
    }
}
