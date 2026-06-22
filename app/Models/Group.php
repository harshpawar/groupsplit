<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Group extends Model
{
    protected $fillable = [
        'name',
        'admin_id',
        'invite_token',
    ];

    protected static function booted(): void
    {
        static::creating(function (Group $group) {
            if (empty($group->invite_token)) {
                $group->invite_token = (string) Str::uuid();
            }
        });
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function inviteUrl(): string
    {
        return route('groups.join', $this->invite_token);
    }

    public function isAdmin(User $user): bool
    {
        return $this->admin_id === $user->id;
    }

    public function hasMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    public function isSettled(): bool
    {
        // A group is settled if all bill splits are settled or if there are no bills
        $billSplits = $this->bills()
            ->with('splits')
            ->get()
            ->flatMap->splits;

        if ($billSplits->isEmpty()) {
            return true; // No bills means settled
        }

        return $billSplits->every(fn ($split) => $split->status->value === 'settled');
    }

    public function unsettledSplitsCount(): int
    {
        return $this->bills()
            ->with('splits')
            ->get()
            ->flatMap->splits
            ->filter(fn ($split) => $split->status->value !== 'settled')
            ->count();
    }

    public function settledPercentage(): int
    {
        $billSplits = $this->bills()
            ->with('splits')
            ->get()
            ->flatMap->splits;

        if ($billSplits->isEmpty()) {
            return 100;
        }

        $settled = $billSplits->filter(fn ($split) => $split->status->value === 'settled')->count();

        return (int) (($settled / $billSplits->count()) * 100);
    }
}
