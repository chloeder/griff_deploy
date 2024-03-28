<?php

namespace App\Livewire;

use App\Models\PerencanaanPerjalananPermanent;
use App\Models\TransaksiProduk;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\Widget;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;

class FormTransaksiProdukWidget extends Widget implements HasForms
{

  use InteractsWithForms;
  protected static string $view = 'livewire.form-transaksi-produk-widget';
  protected int | string | array $columnSpan = 'full';
  public $diskon_total, $pjpId;
  public function mount($pjpId)
  {
    $this->pjpId = $pjpId;
  }
  public function form(Form $form): Form
  {
    return $form
      ->schema([
        TextInput::make('diskon_total')
          ->live()
          ->afterStateUpdated(function ($state) {
            TransaksiProduk::where('perencanaan_perjalanan_permanent_id', $this->pjpId)->update(['diskon_total' => $state]);
            return $state;
          })
          ->label('Diskon Total')
          ->numeric()
          ->required(),
      ])
      ->model(TransaksiProduk::class);
  }
}
