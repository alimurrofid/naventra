<?php

namespace App\Modules\Accounting\Listeners;

use App\Modules\Accounting\Events\TransactionCreated;
use App\Modules\Accounting\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * WriteAuditLog Listener
 *
 * Logs transaction creation to the audit_logs table.
 * This is an example of event-driven extensibility.
 */
class WriteAuditLog
{
    public function handle(TransactionCreated $event): void
    {
        try {
            AuditLog::create([
                'auditable_type' => get_class($event->transaction),
                'auditable_id'   => $event->transaction->id,
                'action'         => 'created',
                'module'         => 'accounting',
                'description'    => "Transaction {$event->transaction->transaction_number} created.",
                'old_values'     => null,
                'new_values'     => $event->transaction->toArray(),
                'user_id'        => Auth::id(),
                'ip_address'     => request()?->ip(),
            ]);
        } catch (\Throwable $e) {
            // Audit logging should never break the main flow
            Log::error('Failed to write audit log: ' . $e->getMessage(), [
                'transaction_id' => $event->transaction->id,
                'exception'      => $e,
            ]);
        }
    }
}
