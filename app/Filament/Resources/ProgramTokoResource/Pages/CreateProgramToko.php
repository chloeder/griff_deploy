<?php

namespace App\Filament\Resources\ProgramTokoResource\Pages;

use App\Filament\Resources\ProgramTokoResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProgramToko extends CreateRecord
{
  protected static bool $canCreateAnother = false;

  protected static string $resource = ProgramTokoResource::class;
  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }

  protected function mutateFormDataBeforeCreate(array $data): array
  {
    $data['tanggal_pembuatan'] = Carbon::now()->format('F Y');

    return $data;
  }
}
