<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('auditable_type', 255);
            $table->unsignedBigInteger('auditable_id');
            $table->string('action', 50)->comment('created, updated, deleted');
            $table->string('module', 50);
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();

            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Indexes
            $table->index(['auditable_type', 'auditable_id']);
            $table->index('action');
            $table->index('module');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
