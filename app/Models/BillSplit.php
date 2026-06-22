<?php

namespace App\Models;

use App\Enums\SplitStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillSplit extends Model
{
    protected $fillable = [
        'bill_id',
        'group_member_id',
        'user_id',
        'share_amount',
        'approved_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'share_amount' => 'decimal:2',
            'approved_amount' => 'decimal:2',
            'status' => SplitStatus::class,
        ];
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function groupMember(): BelongsTo
    {
        return $this->belongsTo(GroupMember::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function remainingAmount(): string
    {
        $remaining = (float) $this->share_amount - (float) $this->approved_amount;

        return number_format(max(0, $remaining), 2, '.', '');
    }

    public function belongsToUser(User $user): bool
    {
        if ($this->user_id === $user->id) {
            return true;
        }

        $normalizedMobile = User::normalizeMobile($user->mobile);

        return $normalizedMobile
            && $normalizedMobile === User::normalizeMobile($this->groupMember?->mobile);
    }
}
