<?php

namespace App\Filament\Resources\StockKeepingUnitResource\Pages;

use App\Filament\Resources\StockKeepingUnitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStockKeepingUnit extends EditRecord
{
  protected static string $resource = StockKeepingUnitResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\DeleteAction::make(),
    ];
  }
  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
