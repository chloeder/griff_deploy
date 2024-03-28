<?php

namespace Database\Seeders;

use App\Models\DataKaryawan;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DataKaryawanSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $user = [
      [
        'nama' => 'FENNY KARMAWAN',
        'status' => 'Aktif',
        'tanggal_aktif' => Carbon::now(),
      ],
      [
        'nama' => 'MEGA ASRI',
        'status' => 'Aktif',
        'tanggal_aktif' => Carbon::now(),
      ],
      [
        'nama' => 'RUSLI BARWA',
        'status' => 'Aktif',
        'tanggal_aktif' => Carbon::now(),
      ],
      [
        'nama' => 'RINI',
        'status' => 'Aktif',
        'tanggal_aktif' => Carbon::now(),
      ],
      [
        'nama' => 'FINO MUMEK',
        'status' => 'Aktif',
        'tanggal_aktif' => Carbon::now(),
      ],
      [
        'nama' => 'JONO',
        'status' => 'Aktif',
        'tanggal_aktif' => Carbon::now(),
      ],
    ];
    foreach ($user as $u) {
      DataKaryawan::create($u);
    }
  }
}
