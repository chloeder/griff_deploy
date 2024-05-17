<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramToko extends Model
{
  use HasFactory;

  protected $casts = [
    'tanggal_pembuatan' => 'date',
  ];

  protected $fillable = [
    'toko_id',
    'sewa_display',
    'sewa_target',
    'cashback',
    'cashback_target',
    'omset_faktur',
    'is_disabled',
    'tanggal_pembuatan',
  ];

  public function toko(): BelongsTo
  {
    return $this->belongsTo(Toko::class, 'toko_id');
  }
  public function transaksi(): HasMany
  {
    return $this->hasMany(TransaksiProduk::class);
  }
}
