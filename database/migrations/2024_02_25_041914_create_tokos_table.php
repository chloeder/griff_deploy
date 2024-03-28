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
    Schema::create('tokos', function (Blueprint $table) {
      $table->id();
      $table->uuid()->unique();
      $table->foreignId('leader_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
      $table->foreignId('klaster_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
      $table->foreignId('sub_klaster_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
      $table->unsignedBigInteger('sales_marketing_id');
      $table->foreign('sales_marketing_id')->references('user_id')->on('sales')->onDelete('cascade')->onUpdate('cascade');
      $table->unsignedBigInteger('sales_promotion_id')->nullable();
      $table->foreign('sales_promotion_id')->references('user_id')->on('sales')->onDelete('cascade')->onUpdate('cascade');
      $table->string('nama');
      $table->string('tipe_toko');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('tokos');
  }
};
