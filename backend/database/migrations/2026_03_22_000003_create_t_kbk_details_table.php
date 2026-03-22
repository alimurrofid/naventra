<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_kbk_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kbk_id')->constrained('t_kbk')->cascadeOnDelete();
            $table->foreignId('coa_id')->constrained('m_coa');
            $table->string('description', 500)->nullable();
            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('credit', 18, 2)->default(0);

            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Indexes
            $table->index('kbk_id');
            $table->index('coa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_kbk_details');
    }
};
