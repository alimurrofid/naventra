<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('example_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('example_id')->constrained('examples')->cascadeOnDelete();
            $table->string('item_name', 255);
            $table->integer('qty');
            $table->decimal('price', 15, 2);
            
            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('example_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('example_details');
    }
};
