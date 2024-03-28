<?php

namespace App\Filament\Resources\PerencanaanPerjalananPermanentResource\Pages;

use App\Filament\Resources\PerencanaanPerjalananPermanentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPerencanaanPerjalananPermanents extends ListRecords
{
    protected static string $resource = PerencanaanPerjalananPermanentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    
}
