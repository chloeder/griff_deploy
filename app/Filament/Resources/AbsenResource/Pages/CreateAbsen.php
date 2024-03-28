<?php

namespace App\Filament\Resources\AbsenResource\Pages;

use App\Filament\Resources\AbsenResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAbsen extends CreateRecord
{
  protected static bool $canCreateAnother = false;

  protected static string $resource = AbsenResource::class;

  protected function mutateFormDataBeforeCreate(array $data): array
  {
    $data['user_id'] = auth()->user()->id;
    $data['tanggal_absen'] = Carbon::now()->format('Y-m-d H:i:s');
    if ($data['keterangan_absen'] === 'Hadir') {
      $data['tanggal_masuk'] = Carbon::now()->format('Y-m-d');
      $data['waktu_masuk'] = Carbon::now()->format('H:i:s');
    } else {
      $data['lokasi_masuk'] = null;
      $data['tanggal_masuk'] = null;
      $data['waktu_masuk'] = null;
    }
    return $data;
  }
  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
