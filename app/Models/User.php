<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Ramsey\Uuid\Uuid;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable;

  public function isAdmin()
  {
    return $this->role === 'Admin';
  }
  public function isLeader()
  {
    return $this->role === 'Leader';
  }
  public function isSesm()
  {
    return $this->role === 'SE/SM';
  }
  public function isSpg()
  {
    return $this->role === 'SPG';
  }
  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'username',
    'role',
    'password',
    'data_karyawan_id',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
  ];

  /**
   * Get the karyawan that owns the User
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function karyawan(): BelongsTo
  {
    return $this->belongsTo(DataKaryawan::class, 'data_karyawan_id', 'id');
  }
}
