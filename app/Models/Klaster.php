<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

class Klaster extends Model
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
    'area',
    'nama',
  ];

  /**
   * Get the leader that owns the Klaster
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function leader(): BelongsTo
  {
    return $this->belongsTo(Leader::class, 'leader_id', 'id');
  }
}
