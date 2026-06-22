<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupMember extends Model
{
    protected $fillable = [
        'group_id',
        'user_id',
        'mobile',
        'is_admin',
        'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'is_admin' => 'boolean',
            'joined_at' => 'datetime',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function billSplits(): HasMany
    {
        return $this->hasMany(BillSplit::class);
    }

    public function displayName(): string
    {
        return $this->user?->name ?? $this->mobile;
    }
}
