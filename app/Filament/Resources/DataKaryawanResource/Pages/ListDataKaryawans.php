<?php

namespace App\Filament\Resources\DataKaryawanResource\Pages;

use App\Filament\Resources\DataKaryawanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDataKaryawans extends ListRecords
{
    protected static string $resource = DataKaryawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
