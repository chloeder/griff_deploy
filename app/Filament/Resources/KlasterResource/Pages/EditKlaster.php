<?php

namespace App\Filament\Resources\KlasterResource\Pages;

use App\Filament\Resources\KlasterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKlaster extends EditRecord
{
  protected static string $resource = KlasterResource::class;

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
