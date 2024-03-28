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
    Schema::create('absens', function (Blueprint $table) {
      $table->id();
      $table->uuid()->unique();
      $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
      $table->string('keterangan_absen');
      $table->date('tanggal_masuk')->nullable();
      $table->time('waktu_masuk', $precision = 0)->nullable();
      $table->string('lokasi_masuk')->nullable();
      $table->date('tanggal_keluar')->nullable();
      $table->time('waktu_keluar', $precision = 0)->nullable();
      $table->string('lokasi_keluar')->nullable();
      $table->string('status_absen')->nullable();
      $table->dateTime('tanggal_absen');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('absens');
  }
};
