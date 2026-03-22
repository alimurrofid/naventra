<?php

namespace App\Modules\Accounting\Events;

use App\Modules\Accounting\Transaction\TKbk\Models\TKbk;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * TransactionCreated Event
 *
 * Fired after a transaction is created and journalized.
 * Used for extensibility: audit logging, notifications, dashboard updates.
 * NOT used for core accounting logic (that's handled by direct JournalEngine calls).
 */
class TransactionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly TKbk $transaction,
    ) {}
}
