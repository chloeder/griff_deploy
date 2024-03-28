<?php

namespace App\Filament\Resources\KlasterResource\Pages;

use App\Filament\Resources\KlasterResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKlaster extends CreateRecord
{
  protected static bool $canCreateAnother = false;

  protected static string $resource = KlasterResource::class;
  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
