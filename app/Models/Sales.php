<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

class Sales extends Model
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
    'user_id',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
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
}
