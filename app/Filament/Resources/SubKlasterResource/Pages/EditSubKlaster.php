<?php

namespace App\Filament\Resources\SubKlasterResource\Pages;

use App\Filament\Resources\SubKlasterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubKlaster extends EditRecord
{
  protected static string $resource = SubKlasterResource::class;

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
