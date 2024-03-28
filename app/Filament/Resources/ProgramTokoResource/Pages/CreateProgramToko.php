<?php

namespace App\Filament\Resources\ProgramTokoResource\Pages;

use App\Filament\Resources\ProgramTokoResource;
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
}
