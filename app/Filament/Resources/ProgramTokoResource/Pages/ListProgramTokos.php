<?php

namespace App\Filament\Resources\ProgramTokoResource\Pages;

use App\Filament\Resources\ProgramTokoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProgramTokos extends ListRecords
{
    protected static string $resource = ProgramTokoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
