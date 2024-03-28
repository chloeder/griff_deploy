<?php

namespace App\Livewire;

use App\Models\PerencanaanPerjalananPermanent;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class TransaksiNoPo extends Component implements HasTable, HasForms
{
  use InteractsWithTable;
  use InteractsWithForms;
  public $pjpId;

  public function mount($id)
  {
    $this->pjpId = $id;
  }
  public function table(Table $table): Table
  {
    return $table
      ->headerActions([
        ExportAction::make()->exports([
          ExcelExport::make('table')->fromTable()
            ->withFilename('Transaksi NO PO - ' . date('Y-m-d '))
        ])->hidden(Auth::user()->role === 'SE/SM' || Auth::user()->role === 'SPG'),
      ])
      ->query(\App\Models\TransaksiNoPo::query()->where('perencanaan_perjalanan_permanent_id', $this->pjpId))
      ->groups([
        Group::make('perencanaan.toko.nama')
          ->label('TOKO')
          ->collapsible(),
        Group::make('sku.sku')
          ->label('SKU')
          ->collapsible(),
        Group::make('perencanaan.tanggal')
          ->label('TANGGAL')
          ->collapsible(),
        Group::make('sales.user.username')
          ->label('SALES')
          ->collapsible(),
      ])
      ->columns([
        TextColumn::make('perencanaan.toko.nama')
          ->searchable()
          ->sortable(),
        TextColumn::make('sales.user.username')
          ->label('SALES')
          ->toggleable(isToggledHiddenByDefault: true)
          ->searchable()
          ->sortable(),
        SelectColumn::make('alasan')
          ->label('Alasan No PO')
          ->options([
            'STOCK CUKUP' => 'STOCK CUKUP',
            'SEPI PENGUNJUNG' => 'SEPI PENGUNJUNG',
            'ORDER BERIKUT' => 'ORDER BERIKUT',
            'TEMPAT PAJANG TIDAK ADA' => 'TEMPAT PAJANG TIDAK ADA',
            'BELUM MAU ORDER PRODUK GRIFF' => 'BELUM MAU ORDER PRODUK GRIFF',
          ])
          ->state(function ($record): ?string {
            PerencanaanPerjalananPermanent::find($record->perencanaan_perjalanan_permanent_id)->update([
              'alasan' => $record->alasan
            ]);
            return $record->alasan ?? null;
          })
          ->sortable()
          ->searchable(),
        TextColumn::make('created_at')
          ->label('TANGGAL')
          ->date()
      ])
      ->filters([])
      ->actions([
        // Tables\Actions\EditAction::make(),
      ])
      ->bulkActions([]);
  }
  public function render()
  {
    return view('livewire.transaksi-no-po');
  }
}
