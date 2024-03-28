<?php

namespace App\Filament\Resources\DataKaryawanResource\Pages;

use App\Filament\Resources\DataKaryawanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDataKaryawan extends EditRecord
{
  protected static string $resource = DataKaryawanResource::class;

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
