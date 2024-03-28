<?php

namespace Database\Seeders;

use App\Models\Klaster;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KlasterSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $user = [
      [
        'leader_id' => 1,
        'area' => 'PAPUA 1',
        'nama' => 'JAYAPURA',
      ],
      [
        'leader_id' => 1,
        'area' => 'PAPUA 1',
        'nama' => 'SERUI',
      ],
      [
        'leader_id' => 2,
        'area' => 'PAPUA 2',
        'nama' => 'BIAK',
      ],
      [
        'leader_id' => 2,
        'area' => 'PAPUA 2',
        'nama' => 'TIMIKA',
      ],
      [
        'leader_id' => 2,
        'area' => 'PAPUA 2',
        'nama' => 'NABIRE',
      ],
    ];

    foreach ($user as $u) {
      Klaster::create($u);
    }
  }
}
