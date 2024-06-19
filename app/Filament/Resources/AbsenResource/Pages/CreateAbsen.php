<?php

namespace App\Filament\Resources\AbsenResource\Pages;

use App\Filament\Resources\AbsenResource;
use App\Models\Absen;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateAbsen extends CreateRecord
{
  protected static bool $canCreateAnother = false;

  protected static string $resource = AbsenResource::class;

  protected function mutateFormDataBeforeCreate(array $data): array
  {
    $count = Absen::where('user_id', auth()->user()->id)->whereDate('tanggal_absen', Carbon::now()->format('Y-m-d'))->count();
    $data['user_id'] = auth()->user()->id;
    $data['tanggal_absen'] = Carbon::now()->format('Y-m-d H:i:s');
    if ($count >= 1) {
      Notification::make()
        ->title('Anda mencapai batas maksimal pengisian absen hari ini')
        ->danger()
        ->send();
      $this->halt();
    } else {
      if ($data['keterangan_absen'] == 'Hadir') {
        $data['tanggal_masuk'] = Carbon::now()->format('Y-m-d');
        $data['waktu_masuk'] = Carbon::now()->format('H:i:s');
      } elseif ($data['keterangan_absen'] == 'Izin') {
        $data['lokasi_masuk'] = null;
        $data['tanggal_masuk'] = null;
        $data['waktu_masuk'] = null;
      } elseif ($data['keterangan_absen'] == 'Sakit') {
        $data['lokasi_masuk'] = null;
        $data['tanggal_masuk'] = null;
        $data['waktu_masuk'] = null;
      } elseif ($data['keterangan_absen'] == 'Alpa') {
        $data['lokasi_masuk'] = null;
        $data['tanggal_masuk'] = null;
        $data['waktu_masuk'] = null;
      }
    }
    return $data;
  }
  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
