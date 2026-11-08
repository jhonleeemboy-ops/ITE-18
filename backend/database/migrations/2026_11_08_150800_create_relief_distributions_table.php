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
      Schema::create('relief_distributions', function (Blueprint      $table) {
         $table->id();
         $table->foreignId('household_id')->constrained()->cascadeOnDelete();
         $table->foreignId('relief_item_id')->constrained()->cascadeOnDelete();
         $table->unsignedInteger('quantity');
         $table->timestamp('distributed_at')->useCurrent();
         $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relief_distributions');
    }
};
