<?php

namespace App\Filament\Resources\PerencanaanPerjalananPermanentStockResource\Pages;

use App\Filament\Resources\PerencanaanPerjalananPermanentStockResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPerencanaanPerjalananPermanentStock extends EditRecord
{
  protected static string $resource = PerencanaanPerjalananPermanentStockResource::class;
  protected static ?string $title = 'Ubah PJP Stock';

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
