<?php

namespace App\Filament\Resources\StockKeepingUnitResource\Pages;

use App\Filament\Resources\StockKeepingUnitResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStockKeepingUnit extends CreateRecord
{
  protected static bool $canCreateAnother = false;

  protected static string $resource = StockKeepingUnitResource::class;
  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
