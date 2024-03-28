<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiNoPo extends Model
{
  use HasFactory;

  protected $fillable = [
    'perencanaan_perjalanan_permanent_id',
    'alasan',
    'sales_id'
  ];

  public function perencanaan(): BelongsTo
  {
    return $this->belongsTo(PerencanaanPerjalananPermanent::class, 'perencanaan_perjalanan_permanent_id');
  }

  public function sales()
  {
    return $this->belongsTo(Sales::class, 'sales_id', 'user_id');
  }
}
