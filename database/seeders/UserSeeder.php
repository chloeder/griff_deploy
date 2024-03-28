<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $users = [
      [
        'username' => 'ADMINISTRATOR',
        'password' => Hash::make('password'),
        'role' => 'Admin',
      ],
      [
        'username' => 'LEADER PAPUA 1',
        'password' => Hash::make('password'),
        'role' => 'Leader',
        'data_karyawan_id' => 1,

      ],
      [
        'username' => 'LEADER PAPUA 2',
        'password' => Hash::make('password'),
        'role' => 'Leader',
        'data_karyawan_id' => 2,

      ],
      [
        'username' => 'SE JAYAPURA 1',
        'password' => Hash::make('password'),
        'role' => 'SE/SM',
        'data_karyawan_id' => 3,

      ],
      [
        'username' => 'SE JAYAPURA 2',
        'password' => Hash::make('password'),
        'role' => 'SE/SM',
        'data_karyawan_id' => 4,

      ],
      [
        'username' => 'SPG JAYAPURA 1',
        'password' => Hash::make('password'),
        'role' => 'SPG',
        'data_karyawan_id' => 5,

      ],
      [
        'username' => 'SPG JAYAPURA 2',
        'password' => Hash::make('password'),
        'role' => 'SPG',
        'data_karyawan_id' => 6,
      ]
    ];

    foreach ($users as $user) {
      \App\Models\User::create($user);
    }
  }
}
