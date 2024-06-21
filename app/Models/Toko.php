<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;

class Toko extends Model
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
    'leader_id',
    'klaster_id',
    'sub_klaster_id',
    'sales_marketing_id',
    'sales_promotion_id',
    'nama_toko',
    'tipe_toko',
    'status',
  ];

  public function leader(): BelongsTo
  {
    return $this->belongsTo(Leader::class);
  }
  public function klaster(): BelongsTo
  {
    return $this->belongsTo(Klaster::class);
  }
  public function sub_klaster(): BelongsTo
  {
    return $this->belongsTo(SubKlaster::class);
  }
  public function sales_marketing(): BelongsTo
  {
    return $this->belongsTo(Sales::class, 'sales_marketing_id', 'user_id');
  }

  public function sales_promotion(): BelongsTo
  {
    return $this->belongsTo(Sales::class, 'sales_promotion_id', 'user_id');
  }

  /**
   * Get all of the perencanaan for the Toko
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function perencanaan(): HasMany
  {
    return $this->hasMany(PerencanaanPerjalananPermanent::class);
  }
}
