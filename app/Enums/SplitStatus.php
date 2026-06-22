<?php

namespace App\Enums;

enum SplitStatus: string
{
    case Pending = 'pending';
    case PartiallyPaid = 'partially_paid';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Overpaid = 'overpaid';
    case Settled = 'settled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::PartiallyPaid => 'Partially Paid',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Overpaid => 'Overpaid',
            self::Settled => 'Settled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'bg-gray-100 text-gray-800',
            self::PartiallyPaid => 'bg-yellow-100 text-yellow-800',
            self::Approved => 'bg-blue-100 text-blue-800',
            self::Rejected => 'bg-red-100 text-red-800',
            self::Overpaid => 'bg-orange-100 text-orange-800',
            self::Settled => 'bg-green-100 text-green-800',
        };
    }
}
