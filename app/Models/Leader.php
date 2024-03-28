<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;

class Leader extends Model
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
    'nama',
    'user_id',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }

  public function karyawan(): HasMany
  {
    return $this->hasMany(DataKaryawan::class);
  }
}
