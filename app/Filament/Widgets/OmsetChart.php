<?php

namespace App\Filament\Widgets;

use App\Models\PerencanaanPerjalananPermanent;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class OmsetChart extends ChartWidget
{
  protected static ?string $heading = 'Chart';
  protected static ?string $pollingInterval = '10s';
  protected static bool $isLazy = false;
  protected static ?string $maxHeight = '500px';
  protected static ?int $sort = 2;

  protected int | string | array $columnSpan = 'full';

  protected function getData(): array
  {
    $data = Trend::model(PerencanaanPerjalananPermanent::class)
      ->between(
        start: now()->startOfYear(),
        end: now()->endOfYear(),
      )
      ->perMonth()
      ->dateColumn('tanggal')
      ->sum('omset_po');

    return [
      'datasets' => [
        [
          'label' => 'Omset',
          'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
        ],
      ],
      'labels' => $data->map(function (TrendValue $value) {
        $date = Carbon::parse($value->date)->format('M');
        return $date;
      }),
    ];
  }
  protected function getType(): string
  {
    return 'line';
  }
}
