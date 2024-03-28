<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;

class StockKeepingUnit extends Model
{
  use HasFactory;
  protected static function boot()
  {
    parent::boot();

    static::creating(function ($model) {
      $model->uuid = Uuid::uuid4()->toString();
    });
  }
  public function getRouteKeyName(): string
  {
    return 'uuid';
  }

  protected $fillable = [
    'uuid',
    'sku',
    'barcode',
    'rbp',
  ];

  // public function perencanaanPerjalananPermanents(): BelongsToMany
  // {
  //   return $this->belongsToMany(PerencanaanPerjalananPermanent::class, 'transaksi_produks');
  // }

  /**
   * Get all of the stock for the StockKeepingUnit
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function stock(): HasMany
  {
    return $this->hasMany(TransaksiStock::class);
  }

  
}
