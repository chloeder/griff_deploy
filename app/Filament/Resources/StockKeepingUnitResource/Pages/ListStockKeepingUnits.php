<?php

namespace App\Filament\Resources\StockKeepingUnitResource\Pages;

use App\Filament\Resources\StockKeepingUnitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStockKeepingUnits extends ListRecords
{
    protected static string $resource = StockKeepingUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
