<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained('journals')->cascadeOnDelete();
            $table->foreignId('coa_id')->constrained('m_coa');
            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('credit', 18, 2)->default(0);

            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Indexes
            $table->index('journal_id');
            $table->index('coa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_details');
    }
};
