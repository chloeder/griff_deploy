<?php

namespace App\Filament\Resources\ProgramTokoResource\Pages;

use App\Filament\Resources\ProgramTokoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProgramToko extends EditRecord
{
  protected static string $resource = ProgramTokoResource::class;

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
