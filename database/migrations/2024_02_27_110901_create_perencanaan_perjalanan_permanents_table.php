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
    Schema::create('perencanaan_perjalanan_permanents', function (Blueprint $table) {
      $table->id();
      $table->uuid()->unique();
      $table->foreignId('leader_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
      $table->foreignId('klaster_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
      $table->foreignId('sub_klaster_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
      $table->unsignedBigInteger('sales_id');
      $table->foreign('sales_id')->references('user_id')->on('sales')->onDelete('cascade')->onUpdate('cascade');
      $table->foreignId('toko_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
      $table->string('tanggal')->nullable();
      $table->bigInteger('omset_po')->default(0)->nullable();
      $table->string('alasan')->nullable();
      $table->string('status')->default('Pending')->nullable();
      $table->string('pjp_status');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('perencanaan_perjalanan_permanents');
  }
};
