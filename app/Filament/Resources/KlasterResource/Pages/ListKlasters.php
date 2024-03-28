<?php

namespace App\Filament\Resources\KlasterResource\Pages;

use App\Filament\Resources\KlasterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKlasters extends ListRecords
{
    protected static string $resource = KlasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
