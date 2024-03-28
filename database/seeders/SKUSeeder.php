<?php

namespace Database\Seeders;

use App\Models\StockKeepingUnit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SKUSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $user = [
      [
        'sku' => 'EVANGELINE MUSK EDP RED / 100 ML / 36',
        'barcode' => '8997017642908',
        'rbp' => 30000,
      ],
      [
        'sku' => 'EVANGELINE MUSK EDP BLUE / 100 ML / 36',
        'barcode' => '8997017642885',
        'rbp' => 30000,
      ],
      [
        'sku' => 'EVANGELINE MUSK EDP BLACK / 100 ML / 36',
        'barcode' => '8997017642878',
        'rbp' => 30000,
      ],
      [
        'sku' => 'EVANGELINE MUSK EDP WHITE / 100 ML / 36',
        'barcode' => '8997017642922',
        'rbp' => 30000,
      ],
      [
        'sku' => 'EVANGELINE MUSK EDP PINK / 100 ML / 36',
        'barcode' => '8997017642892',
        'rbp' => 30000,
      ],
      [
        'sku' => 'EVANGELINE MUSK EDP VIOLET / 100 ML / 36',
        'barcode' => '8997017642915',
        'rbp' => 30000,
      ],
      [
        'sku' => 'EVANGELINE SELECTION EDP COCONUT FANTASY / 100 ML / 36',
        'barcode' => '8997017642953',
        'rbp' => 30000,
      ],
      [
        'sku' => 'EVANGELINE SELECTION EDP GREEN TEA / 100 ML / 36',
        'barcode' => '8997017642960',
        'rbp' => 30000,
      ],
      [
        'sku' => 'EVANGELINE SELECTION EDP ROYAL LAVENDER / 100 ML / 36',
        'barcode' => '8997017643011',
        'rbp' => 30000,
      ],
      [
        'sku' => 'EVANGELINE SELECTION EDP BLACK VANILLA / 100 ML / 36',
        'barcode' => '8997017642939',
        'rbp' => 30000,
      ],
      [
        'sku' => 'EVANGELINE SELECTION EDP SWEET STRAWBERRY / 100 ML / 36',
        'barcode' => '8997017643103',
        'rbp' => 30000,
      ],
      [
        'sku' => 'EVANGELINE SELECTION EDP CHERRY BLOSSOM / 100 ML / 36',
        'barcode' => '8997017642946',
        'rbp' => 30000,
      ],
      [
        'sku' => 'EVANGELINE SELECTION EDP SUGAR BISCUIT / 100 ML / 36',
        'barcode' => '8997017643028',
        'rbp' => 30000,
      ],
      [
        'sku' => 'EVANGELINE SELECTION EDP MANGO TEMPTATION / 100 ML / 36',
        'barcode' => '8997017642984',
        'rbp' => 30000,
      ],
      [
        'sku' => 'EVANGELINE SELECTION EDP ORANGE CITRUS / 100 ML / 36',
        'barcode' => '8997017642991',
        'rbp' => 30000,
      ],
      [
        'sku' => 'EVANGELINE SELECTION EDP JUICY PEAR / 100 ML / 36',
        'barcode' => '8997017642977',
        'rbp' => 30000,
      ],
      [
        'sku' => 'EVANGELINE SELECTION EDP SUMMER PEACH / 100 ML / 36',
        'barcode' => '8997017643097',
        'rbp' => 30000,
      ],
      [
        'sku' => 'EVANGELINE SELECTION EDP RAW ALMOND / 100 ML / 36',
        'barcode' => '8997017643004',
        'rbp' => 30000,
      ],
      [
        'sku' => 'EVANGELINE PREMIUM EDP BABY POWDER / 100 ML / 36',
        'barcode' => '8997017643325',
        'rbp' => 30000,
      ],
      [
        'sku' => 'EVANGELINE PREMIUM EDP BAKKARAT / 100 ML / 36',
        'barcode' => '8997017643332',
        'rbp' => 30000,
      ],
    ];

    foreach ($user as $u) {
      StockKeepingUnit::create($u);
    }
  }
}
