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
    Schema::create('sub_klasters', function (Blueprint $table) {
      $table->id();
      $table->uuid()->unique();
      $table->foreignId('leader_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
      $table->foreignId('klaster_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
      $table->string('nama');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('sub_klasters');
  }
};
