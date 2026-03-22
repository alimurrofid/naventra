<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_coa', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 255);
            $table->foreignId('parent_id')->nullable()->constrained('m_coa')->nullOnDelete();
            $table->integer('level')->default(1);
            $table->string('type', 50)->comment('asset, liability, equity, revenue, expense');
            $table->boolean('is_active')->default(true);
            $table->string('description', 500)->nullable();

            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('type');
            $table->index('is_active');
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_coa');
    }
};
