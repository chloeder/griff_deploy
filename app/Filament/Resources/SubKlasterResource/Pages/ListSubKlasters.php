<?php

namespace App\Filament\Resources\SubKlasterResource\Pages;

use App\Filament\Resources\SubKlasterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubKlasters extends ListRecords
{
    protected static string $resource = SubKlasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
