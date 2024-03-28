<?php

namespace App\Filament\Widgets;

use App\Models\DataKaryawan;
use App\Models\PerencanaanPerjalananPermanent;
use App\Models\StockKeepingUnit;
use App\Models\Toko;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
  protected function getStats(): array
  {
    return [
      Stat::make('Total Toko', Toko::count())
        ->description('Jumlah total Toko')
        ->chart([1, 3, 5, 10, 100,])
        ->color('success'),
      Stat::make('Total Karyawan', DataKaryawan::count())
        ->description('Jumlah total Karyawan')
        ->chart([1, 3, 5, 10, 100,])
        ->color('success'),
      Stat::make('Total SKU', StockKeepingUnit::count())
        ->description('Jumlah total Karyawan')
        ->chart([1, 3, 5, 10, 100,])
        ->color('success'),
    ];
  }
}
