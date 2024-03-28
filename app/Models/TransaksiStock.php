<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiStock extends Model
{
  use HasFactory;

  protected $fillable = [
    'pjp_stock_id',
    'stock_keeping_unit_id',
    'sales_id',
    'sdm',
    'nilai_sdm',
    'sdt',
    'nilai_sdt',
    'sdp',
    'nilai_sdp',
    'sell_stock',
    'nilai_sell_stock',
  ];

  public function perencanaan()
  {
    return $this->belongsTo(PerencanaanPerjalananPermanentStock::class, 'pjp_stock_id');
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
