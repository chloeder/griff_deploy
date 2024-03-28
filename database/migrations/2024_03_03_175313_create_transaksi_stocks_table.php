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
    Schema::create('transaksi_stocks', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('pjp_stock_id');
      $table->foreign('pjp_stock_id')->references('id')->on('perencanaan_perjalanan_permanent_stocks')->onDelete('cascade')->onUpdate('cascade');
      $table->foreignId('stock_keeping_unit_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
      $table->unsignedBigInteger('sales_id');
      $table->foreign('sales_id')->references('user_id')->on('sales')->onDelete('cascade')->onUpdate('cascade');
      $table->bigInteger('sdm')->default(0)->nullable();
      $table->string('nilai_sdm')->default(0)->nullable();
      $table->bigInteger('sdt')->default(0)->default(0)->nullable();
      $table->string('nilai_sdt')->default(0)->default(0)->nullable();
      $table->bigInteger('sdp')->default(0)->nullable();
      $table->string('nilai_sdp')->default(0)->nullable();
      $table->bigInteger('sell_stock')->default(0)->nullable();
      $table->string('nilai_sell_stock')->default(0)->nullable();
      $table->string('tanggal')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('transaksi_stocks');
  }
};
