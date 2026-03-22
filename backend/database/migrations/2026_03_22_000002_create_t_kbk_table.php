<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_kbk', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number', 30)->unique();
            $table->date('transaction_date');
            $table->string('transaction_type', 30)->comment('cash_in, cash_out, bank_in, bank_out');
            $table->string('description', 500)->nullable();
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->string('status', 20)->default('draft')->comment('draft, posted, voided');

            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('transaction_date');
            $table->index('transaction_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_kbk');
    }
};
