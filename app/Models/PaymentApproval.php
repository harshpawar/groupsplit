<?php

namespace App\Models;

use App\Enums\ApprovalAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentApproval extends Model
{
    protected $fillable = [
        'payment_id',
        'reviewed_by',
        'action',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'action' => ApprovalAction::class,
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
