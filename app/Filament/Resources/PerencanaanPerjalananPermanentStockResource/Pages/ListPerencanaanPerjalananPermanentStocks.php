<?php

namespace App\Filament\Resources\PerencanaanPerjalananPermanentStockResource\Pages;

use App\Filament\Resources\PerencanaanPerjalananPermanentStockResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPerencanaanPerjalananPermanentStocks extends ListRecords
{
    protected static string $resource = PerencanaanPerjalananPermanentStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
