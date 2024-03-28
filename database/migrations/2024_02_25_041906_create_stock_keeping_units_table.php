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
    Schema::create('stock_keeping_units', function (Blueprint $table) {
      $table->id();
      $table->uuid()->unique();
      $table->string('sku')->unique();
      $table->string('barcode')->unique();
      $table->string('rbp');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('stock_keeping_units');
  }
};
