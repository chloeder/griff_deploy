<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

class Absen extends Model
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
    'user_id',
    'keterangan_absen',
    'tanggal_masuk',
    'waktu_masuk',
    'lokasi_masuk',
    'tanggal_keluar',
    'waktu_keluar',
    'lokasi_keluar',
    'status_absen',
    'tanggal_absen',
    'status_keluar',
  ];

  /**
   * Get the user that owns the Absen
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }
}
