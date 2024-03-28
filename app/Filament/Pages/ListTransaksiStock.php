<?php

namespace App\Filament\Pages;

use App\Filament\Resources\PerencanaanPerjalananPermanentStockResource;
use Filament\Actions\Action;
use Filament\Pages\Page;

class ListTransaksiStock extends Page
{
  public $pjpId;
  protected static ?string $navigationIcon = 'heroicon-o-document-text';
  protected static string $view = 'filament.pages.list-transaksi-stock';
  protected static bool $shouldRegisterNavigation = false;
  public function mount($id)
  {
    $this->pjpId = $id;
  }

  protected function getHeaderActions(): array
  {
    return [
      Action::make('Kembali')
        ->url(PerencanaanPerjalananPermanentStockResource::getUrl('index')),
    ];
  }
}
