<?php

namespace App\Filament\Resources\PerencanaanPerjalananPermanentResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use App\Models\TransaksiNoPo;
use App\Models\TransaksiStock;
use App\Models\StockKeepingUnit;
use Filament\Actions\CreateAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\PerencanaanPerjalananPermanentResource;
use App\Models\TransaksiProduk;

class EditPerencanaanPerjalananPermanent extends EditRecord
{
  protected static string $resource = PerencanaanPerjalananPermanentResource::class;

  protected function handleRecordUpdate(Model $record, array $data): Model
  {

    $omsets = TransaksiProduk::all();
    $stocks = TransaksiStock::all();
    $no_pos = TransaksiNoPo::all();

    foreach ($omsets as $omset) {
      if ($omset->perencanaan_perjalanan_permanent_id == $record->id) {
        $omset->sales_id = $data['sales_id'];
        $omset->save();
      }
    }
    foreach ($stocks as $stock) {
      if ($stock->perencanaan_perjalanan_permanent_id == $record->id) {
        $stock->sales_id = $data['sales_id'];
        $stock->save();
      }
    }
    foreach ($no_pos as $no_po) {
      if ($no_po->perencanaan_perjalanan_permanent_id == $record->id) {
        $no_po->sales_id = $data['sales_id'];
        $no_po->save();
      }
      // if ($record->sales->user->role === 'SPG') {
      //   $no_po = new TransaksiNoPo();
      //   $no_po->perencanaan_perjalanan_permanent_id = $record->id;
      //   $no_po->sales_id = $data['sales_id'];
      //   $no_po->save();
      // }
    }
    $record->update($data);

    return $record;
  }

  protected function getHeaderActions(): array
  {
    return [
      Actions\Action::make('Kembali')
        ->url(PerencanaanPerjalananPermanentResource::getUrl('index')),
      Actions\DeleteAction::make(),
    ];
  }
  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
