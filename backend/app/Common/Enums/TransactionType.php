<?php

namespace App\Common\Enums;

/**
 * Enum TransactionType
 *
 * Shared transaction type enum used across ERP modules.
 */
enum TransactionType: string
{
    case CASH_IN = 'cash_in';
    case CASH_OUT = 'cash_out';
    case BANK_IN = 'bank_in';
    case BANK_OUT = 'bank_out';
    case JOURNAL = 'journal';
    case ADJUSTMENT = 'adjustment';

    /**
     * Get a human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::CASH_IN => 'Cash In',
            self::CASH_OUT => 'Cash Out',
            self::BANK_IN => 'Bank In',
            self::BANK_OUT => 'Bank Out',
            self::JOURNAL => 'Journal',
            self::ADJUSTMENT => 'Adjustment',
        };
    }
}
