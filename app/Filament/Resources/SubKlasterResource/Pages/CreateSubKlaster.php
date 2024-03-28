<?php

namespace App\Filament\Resources\SubKlasterResource\Pages;

use App\Filament\Resources\SubKlasterResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSubKlaster extends CreateRecord
{
  protected static bool $canCreateAnother = false;
  protected static string $resource = SubKlasterResource::class;
  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
