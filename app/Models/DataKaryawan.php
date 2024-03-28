<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class DataKaryawan extends Model
{
  use HasFactory;

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($model) {
      $model->uuid = Uuid::uuid4()->toString();
    });
  }

  protected $fillable = [
    'uuid',
    'nama',
    'no_rek',
    'bank',
    'cabang',
    'an_nama',
    'status',
    'tanggal_aktif',
  ];
}
