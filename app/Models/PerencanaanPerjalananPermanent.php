<?php

namespace App\Models;

use App\Models\Toko;
use App\Models\Sales;
use Ramsey\Uuid\Uuid;
use App\Models\Leader;
use App\Models\Klaster;
use App\Models\SubKlaster;
use App\Policies\PJPPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerencanaanPerjalananPermanent extends Model
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


  protected $fillable = [
    'uuid',
    'leader_id',
    'klaster_id',
    'sub_klaster_id',
    'sales_id',
    'toko_id',
    'omset_po',
    'alasan',
    'tanggal',
    'pjp_status',
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

  public function transaksis(): HasMany
  {
    return $this->hasMany(TransaksiProduk::class, 'perencanaan_perjalanan_permanent_id');
  }
  public function stock(): HasMany
  {
    return $this->hasMany(TransaksiStock::class);
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
