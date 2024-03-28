<?php

namespace Database\Seeders;

use App\Models\Leader;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeaderSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $user = [
      [
        'nama' => 'LEADER PAPUA 1',
        'user_id' => 2,
      ],
      [
        'nama' => 'LEADER PAPUA 2',
        'user_id' => 3,
      ],
    ];

    foreach ($user as $u) {
      Leader::create($u);
    }
  }
}
