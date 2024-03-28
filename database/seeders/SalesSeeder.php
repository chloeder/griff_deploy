<?php

namespace Database\Seeders;

use App\Models\Sales;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SalesSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $user = [
      [
        'leader_id' => 1,
        'klaster_id' => 1,
        'sub_klaster_id' => 1,
        'user_id' => 4,
      ],
      [
        'leader_id' => 1,
        'klaster_id' => 1,
        'sub_klaster_id' => 1,
        'user_id' => 5,
      ],
      [
        'leader_id' => 1,
        'klaster_id' => 1,
        'sub_klaster_id' => 1,
        'user_id' => 6,
      ],
      [
        'leader_id' => 1,
        'klaster_id' => 1,
        'sub_klaster_id' => 1,
        'user_id' => 7,
      ],
    ];
    foreach ($user as $u) {
      Sales::create($u);
    }
  }
}
