<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;

class PerencanaanPerjalananPermanentStock extends Model
{
  use HasFactory;
  public function isAdmin(User $user)
  {
    return $user->role === 'Admin';
  }
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

  protected $table = 'perencanaan_perjalanan_permanent_stocks';
  protected $fillable = [
    'uuid',
    'leader_id',
    'klaster_id',
    'sub_klaster_id',
    'sales_id',
    'toko_id',
    'tanggal',
    'pjp_status',
    'sell_stocks',
  ];

  /**
   * Get the toko that owns the PerencanaanPerjalananPermanent
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function toko(): BelongsTo
  {
    return $this->belongsTo(Toko::class, 'toko_id', 'id');
  }

  public function leader(): BelongsTo
  {
    return $this->belongsTo(Leader::class, 'leader_id', 'id');
  }
  public function klaster(): BelongsTo
  {
    return $this->belongsTo(Klaster::class, 'klaster_id', 'id');
  }
  public function sub_klaster(): BelongsTo
  {
    return $this->belongsTo(SubKlaster::class, 'sub_klaster_id', 'id');
  }
  public function sales(): BelongsTo
  {
    return $this->belongsTo(Sales::class, 'sales_id', 'user_id');
  }

  public function sku(): BelongsToMany
  {
    return $this->belongsToMany(StockKeepingUnit::class, 'transaksi_produks')->withPivot('qty', 'diskon', 'omset_po');
  }
  public function sku_stock(): BelongsToMany
  {
    return $this->belongsToMany(StockKeepingUnit::class, 'transaksi_stocks')->withPivot('sdm', 'sdt', 'sdp', 'sell_stock');
  }

  /**
   * Get all of the transaksi for the PerencanaanPerjalananPermanent
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function transaksi(): HasMany
  {
    return $this->hasMany(TransaksiProduk::class);
  }
  public function stock(): HasMany
  {
    return $this->hasMany(TransaksiStock::class, 'pjp_stock_id');
  }
  public function no_po(): HasMany
  {
    return $this->hasMany(TransaksiNoPo::class);
  }
  public function program(): HasMany
  {
    return $this->hasMany(ProgramToko::class);
  }
}
