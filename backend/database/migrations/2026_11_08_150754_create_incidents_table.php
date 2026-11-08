<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('incidents', function (Blueprint $table) {
          $table->id();
          $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
          $table->enum('type', ['evacuation', 'relief', 'medical', 'rescue', 'hazard']);
          $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
          $table->enum('status', ['pending', 'in_progress', 'resolved'])->default('pending');
          $table->text('description')->nullable();
          $table->timestamps(); 
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
