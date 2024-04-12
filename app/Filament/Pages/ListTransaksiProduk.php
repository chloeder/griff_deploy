<?php

namespace App\Filament\Pages;

use App\Filament\Resources\PerencanaanPerjalananPermanentResource;
use App\Livewire\FormTransaksiProdukWidget;
use App\Models\TransaksiProduk;
use Filament\Actions\Action;
use Filament\Pages\Page;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListTransaksiProduk extends Page
{
  public $pjpId, $diskon_total;
  protected static ?string $navigationIcon = 'heroicon-o-document-text';
  protected static ?string $title = 'Transaksi PO';

  protected static string $view = 'filament.pages.list-transaksi-produk';
  protected static bool $shouldRegisterNavigation = false;
  public function mount($id)
  {
    $this->pjpId = $id;
  }

  protected function getHeaderActions(): array
  {
    return [
      Action::make('Kembali')
        ->url(PerencanaanPerjalananPermanentResource::getUrl('index')),
    ];
  }

  // protected function getHeaderWidgets(): array
  // {
  //   return [
  //     FormTransaksiProdukWidget::make([
  //       'pjpId' => $this->pjpId,
  //     ]),
  //   ];
  // }
}
