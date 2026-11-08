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
        Schema::create('sms_logs', function (Blueprint       $table) {
          $table->id();
          $table->foreignId('advisory_id')->constrained()->cascadeOnDelete();
          $table->unsignedInteger('recipients')->default(0);
          $table->unsignedInteger('sent')->default(0);    // only increase when provider confirms
          $table->unsignedInteger('failed')->default(0);
          $table->timestamp('sent_at')->nullable();       // null = not confirmed yet
          $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
