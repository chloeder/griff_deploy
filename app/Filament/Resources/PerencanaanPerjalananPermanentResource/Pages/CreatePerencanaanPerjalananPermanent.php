<?php

namespace App\Filament\Resources\PerencanaanPerjalananPermanentResource\Pages;

use App\Filament\Resources\PerencanaanPerjalananPermanentResource;
use App\Models\StockKeepingUnit;
use App\Models\TransaksiNoPo;
use App\Models\TransaksiProduk;
use App\Models\TransaksiStock;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePerencanaanPerjalananPermanent extends CreateRecord
{
  protected static bool $canCreateAnother = false;
  protected static ?string $title = 'Buat PJP Omset';
  protected static string $resource = PerencanaanPerjalananPermanentResource::class;

  protected function handleRecordCreation(array $data): Model
  {
    //insert the student
    $record =  static::getModel()::create($data);

    $sku = StockKeepingUnit::all();

    foreach ($sku as $item) {
      $omset = new TransaksiProduk();
      $omset->stock_keeping_unit_id = $item->id;
      $omset->perencanaan_perjalanan_permanent_id = $record->id;
      $omset->sales_id = $record->sales_id;
      $omset->tanggal = $record->tanggal;
      $omset->save();
    }

    $no_po = new TransaksiNoPo();
    $no_po->perencanaan_perjalanan_permanent_id = $record->id;
    $no_po->sales_id = $record->sales_id;
    $no_po->tanggal = $record->tanggal;
    $no_po->save();

    return $record;
  }

  protected function mutateFormDataBeforeCreate(array $data): array
  {
    $data['pjp_status'] = 'PLAN';

    return $data;
  }

  protected function getHeaderActions(): array
  {
    return [
      Actions\Action::make('Kembali')
        ->url(PerencanaanPerjalananPermanentResource::getUrl('index')),
    ];
  }
  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
