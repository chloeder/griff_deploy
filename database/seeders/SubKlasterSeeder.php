<?php

namespace Database\Seeders;

use App\Models\SubKlaster;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubKlasterSeeder extends Seeder
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
        'nama' => 'JAYAPURA',
      ],
      [
        'leader_id' => 1,
        'klaster_id' => 1,
        'nama' => 'SENTANI',
      ],
      [
        'leader_id' => 2,
        'klaster_id' => 3,
        'nama' => 'BIAK',
      ],
      [
        'leader_id' => 2,
        'klaster_id' => 3,
        'nama' => 'BIAK SUPIOR',
      ],
    ];
    foreach ($user as $u) {
      SubKlaster::create($u);
    }
  }
}
