<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->string('journal_number', 30)->unique();
            $table->string('source', 50)->comment('Source module/table');
            $table->unsignedBigInteger('ref_id')->comment('Reference ID in source table');
            $table->date('journal_date');
            $table->string('description', 500)->nullable();
            $table->decimal('total_debit', 18, 2)->default(0);
            $table->decimal('total_credit', 18, 2)->default(0);

            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('source');
            $table->index('ref_id');
            $table->index('journal_date');
            $table->index(['source', 'ref_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
