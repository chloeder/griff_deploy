<?php

namespace App\Livewire;

use App\Models\PerencanaanPerjalananPermanent;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\RawJs;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class TransaksiProduk extends Component implements HasTable, HasForms
{
  use InteractsWithTable;
  use InteractsWithForms;

  public $pjpId, $leaderId;

  public function mount($id)
  {
    $this->pjpId = $id;
  }
  public function table(Table $table): Table
  {
    return $table
      ->headerActions([
        ExportAction::make()->exports([
          ExcelExport::make()->withColumns([
            Column::make('sku.sku')->heading('SKU'),
            Column::make('sku.rbp')->heading('Harga SKU')->format(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2),
            Column::make('qty')->heading('Qty'),
            Column::make('nilai')->heading('Nilai')->format(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2),
            Column::make('diskon')->heading('Diskon Barang'),
            Column::make('diskon_total')->heading('Diskon Total'),
            Column::make('omset_po')->heading('Omset PO')->format(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2),
          ])
            ->withFilename('Transaksi Omset - ' . date('Y-m-d '))
        ])->hidden(Auth::user()->role === 'SE/SM' || Auth::user()->role === 'SPG'),
      ])
      ->poll('1s')
      ->query(\App\Models\TransaksiProduk::query()->where('perencanaan_perjalanan_permanent_id', $this->pjpId))
      ->columns([
        TextColumn::make('sku.sku')
          ->label('SKU')
          ->searchable(),
        TextColumn::make('sku.rbp')
          ->label('Harga SKU')
          ->searchable()
          ->sortable()
          ->prefix('Rp. ')
          ->numeric(locale: 'id'),
        TextInputColumn::make('qty')
          ->summarize(Sum::make()->label('Total Qty')),
        TextColumn::make('nilai')
          ->state(function ($record): float {
            $record->update(['nilai' => $record->qty * $record->sku->rbp]);
            return $record->nilai ?? 0;
          })
          ->toggleable(isToggledHiddenByDefault: false)
          ->prefix('Rp. ')
          ->numeric(locale: 'id')
          ->summarize(Sum::make()->label('Total Nilai')->money('Rp.'))
          ->sortable(),
        TextInputColumn::make('diskon')
          ->label('Diskon')
          ->placeholder('Contoh : 30'),
        // TextColumn::make('diskon_total')
        //   ->label('Diskon Total')
        //   ->formatStateUsing(function ($state) {
        //     return $state . '%';
        //   })
        //   ->summarize(Average::make()
        //     ->label('Diskon Total')
        //     ->formatStateUsing(function ($state) {
        //       return $state . '%';
        //     })),
        TextColumn::make('omset_po')
          ->label('Omset PO')
          ->state(function ($record): float {
            if ($record->diskon == 0 && $record->diskon_total == 0) {
              $record->update(['omset_po' => $record->qty * $record->sku->rbp]);
            } elseif ($record->diskon !== 0 && $record->diskon_total == 0) {
              $record->update(['omset_po' => ($record->nilai) - ($record->nilai * $record->diskon / 100)]);
            } elseif ($record->diskon == 0 && $record->diskon_total !== 0) {
              $record->update(['omset_po' => ($record->nilai) - ($record->nilai * $record->diskon_total / 100)]);
            } else {
              $record->update(['omset_po' => ($record->nilai) - ($record->nilai * $record->diskon / 100) - ($record->nilai * $record->diskon_total / 100)]);
            }
            PerencanaanPerjalananPermanent::find($record->perencanaan_perjalanan_permanent_id)->update(['omset_po' => $record->where('perencanaan_perjalanan_permanent_id', $record->perencanaan_perjalanan_permanent_id)->sum('omset_po')]);
            return $record->omset_po ?? 0;
          })
          ->toggleable(isToggledHiddenByDefault: false)
          ->prefix('Rp. ')
          ->numeric(locale: 'id')
          ->summarize(Sum::make()->label('Total Omset')->money('Rp.'))
          ->sortable(),

      ])
      ->filters([
        // ...
      ])
      ->actions([
        // ...
      ])
      ->bulkActions([
        // ...
      ]);
  }

  public function render(): View
  {
    return view('livewire.transaksi-produk');
  }
}
