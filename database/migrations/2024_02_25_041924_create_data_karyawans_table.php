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
    Schema::create('data_karyawans', function (Blueprint $table) {
      $table->id();
      $table->uuid()->unique();
      $table->string('nama')->unique();
      $table->string('no_rek')->nullable();
      $table->string('bank')->nullable();
      $table->string('cabang')->nullable();
      $table->string('an_nama')->nullable();
      $table->string('status');
      $table->string('tanggal_aktif')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('data_karyawans');
  }
};
