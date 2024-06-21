<?php

namespace App\Filament\Resources\ProgramTokoResource\Pages;

use App\Filament\Resources\ProgramTokoResource;
use Carbon\Carbon;
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

  protected function mutateFormDataBeforeSave(array $data): array
  {
    $data['tanggal_pembuatan'] = Carbon::now()->format('F Y');

    return $data;
  }
}
