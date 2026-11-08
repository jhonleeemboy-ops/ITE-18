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
        Schema::create('households', function (Blueprint $table) {
           $table->id();
           $table->string('head_name');
           $table->unsignedTinyInteger('purok');           // 1-8
           $table->unsignedSmallInteger('members')->default(1);
           $table->enum('safety', ['safe', 'evacuated', 'needs_help'])->default('safe');
           $table->timestamps();
           $table->index('purok');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('households');
    }
};
