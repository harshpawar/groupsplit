<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    protected $fillable = [
        'bill_split_id',
        'user_id',
        'amount',
        'status',
        'note',
        'marked_paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => PaymentStatus::class,
            'marked_paid_at' => 'datetime',
        ];
    }

    public function billSplit(): BelongsTo
    {
        return $this->belongsTo(BillSplit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(PaymentApproval::class)->latest();
    }
}
