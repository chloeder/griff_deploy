<?php

namespace App\Filament\Pages;

use App\Filament\Resources\PerencanaanPerjalananPermanentResource;
use Filament\Actions\Action;
use Filament\Pages\Page;

class ListTransaksiNoPo extends Page
{
  public $pjpId;
  protected static ?string $navigationIcon = 'heroicon-o-document-text';
  protected static string $view = 'filament.pages.list-transaksi-no-po';
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
}
