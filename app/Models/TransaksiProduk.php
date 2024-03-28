<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class TransaksiProduk extends Model
{
  use HasFactory;


  protected $fillable = [
    'perencanaan_perjalanan_permanent_id',
    'stock_keeping_unit_id',
    'qty',
    'nilai',
    'diskon',
    // 'diskon_total',
    // 'alasan_no_pre_order',
    'omset_po',
    // 'omset_total',
    // 'status_produk'
  ];

  public function perencanaan()
  {
    return $this->belongsTo(PerencanaanPerjalananPermanent::class);
  }
  public function sku()
  {
    return $this->belongsTo(StockKeepingUnit::class, 'stock_keeping_unit_id');
  }
  public function sales()
  {
    return $this->belongsTo(Sales::class, 'sales_id', 'user_id');
  }
}
