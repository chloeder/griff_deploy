<?php

namespace App\Filament\Resources\PerencanaanPerjalananPermanentStockResource\Pages;

use App\Filament\Resources\PerencanaanPerjalananPermanentStockResource;
use App\Models\StockKeepingUnit;
use App\Models\TransaksiStock;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePerencanaanPerjalananPermanentStock extends CreateRecord
{
  protected static bool $canCreateAnother = false;
  protected static ?string $title = 'Buat PJP Stock';

  protected static string $resource = PerencanaanPerjalananPermanentStockResource::class;

  protected function handleRecordCreation(array $data): Model
  {
    //insert the student
    $record =  static::getModel()::create($data);

    $sku = StockKeepingUnit::all();

    foreach ($sku as $item) {
      $stock = new TransaksiStock();
      if ($record->sales->user->role === 'SPG') {
        $stock->stock_keeping_unit_id = $item->id;
        $stock->pjp_stock_id = $record->id;
        $stock->sales_id = $record->sales_id;
        $stock->save();
      }
    }

    return $record;
  }
  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
