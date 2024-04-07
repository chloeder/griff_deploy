<?php

namespace App\Filament\Resources\AbsenResource\Pages;

use App\Filament\Resources\AbsenResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAbsen extends EditRecord
{
  protected static string $resource = AbsenResource::class;
  protected function mutateFormDataBeforeSave(array $data): array
  {
    
    $data['tanggal_keluar'] = Carbon::now()->format('Y-m-d');
    $data['waktu_keluar'] = Carbon::now()->format('H:i:s');
    $data['status_absen'] = 'Proses';
    return $data;
  }

  protected function getHeaderActions(): array
  {
    return [
      Actions\DeleteAction::make(),
    ];
  }
  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
