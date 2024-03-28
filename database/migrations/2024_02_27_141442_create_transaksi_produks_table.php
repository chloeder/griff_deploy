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
    Schema::create('transaksi_produks', function (Blueprint $table) {
      $table->id();
      $table->foreignId('perencanaan_perjalanan_permanent_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
      $table->foreignId('stock_keeping_unit_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
      $table->unsignedBigInteger('sales_id');
      $table->foreign('sales_id')->references('user_id')->on('sales')->onDelete('cascade')->onUpdate('cascade');
      $table->bigInteger('qty')->default(0)->nullable();
      $table->bigInteger('nilai')->default(0)->nullable();
      $table->double('diskon', 8, 2)->default(0)->nullable();
      $table->double('diskon_total', 8, 2)->default(0)->nullable();
      $table->bigInteger('omset_po')->default(0)->nullable();
      $table->string('tanggal')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('transaksi_produks');
  }
};
