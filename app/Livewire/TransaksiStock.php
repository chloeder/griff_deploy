<?php

namespace App\Livewire;

use App\Models\PerencanaanPerjalananPermanentStock;
use Livewire\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class TransaksiStock extends Component implements HasTable, HasForms
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
          ExcelExport::make()->withColumns([
            Column::make('sku.sku')->heading('SKU'),
            Column::make('sku.rbp')->heading('Harga SKU')->format(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2),
            Column::make('sdm')->heading('SDM'),
            Column::make('nilai_sdm')->heading('Nilai SDM')->format(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2),
            Column::make('sdt')->heading('SDT'),
            Column::make('nilai_sdt')->heading('Nilai SDT')->format(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2),
            Column::make('sdp')->heading('SDP'),
            Column::make('nilai_sdp')->heading('Nilai SDP')->format(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2),
            Column::make('sell_stock')->heading('SELL'),
            Column::make('nilai_sell_stock')->heading('Nilai SELL')->format(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2),
          ])
            ->withFilename('Transaksi Stock - ' . date('Y-m-d '))
        ])->hidden(Auth::user()->role === 'SE/SM' || Auth::user()->role === 'SPG'),
      ])
      ->query(\App\Models\TransaksiStock::query()->where('pjp_stock_id', $this->pjpId))
      ->poll('10s')
      ->columns([
        Tables\Columns\TextColumn::make('perencanaan.toko.nama_toko')
          ->toggleable(isToggledHiddenByDefault: true)
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('sku.sku')
          ->label('SKU')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('sku.rbp')
          ->label('Harga SKU')
          ->searchable()
          ->sortable()
          ->prefix('Rp. ')
          ->numeric(locale: 'id'),
        Tables\Columns\TextInputColumn::make('sdm')
          ->label('SDM')
          ->sortable()
          ->summarize(Sum::make()->label('Total SDM')),
        Tables\Columns\TextColumn::make('nilai_sdm')
          ->label('Nilai SDM')
          ->state(function ($record): float {
            $record->update(['nilai_sdm' => $record->sdm * $record->sku->rbp]);
            return $record->nilai_sdm ?? 0;
          })
          ->toggleable(isToggledHiddenByDefault: false)
          ->prefix('Rp. ')
          ->numeric(locale: 'id')
          ->summarize(Sum::make()->label('Total Nilai SDM')->money('Rp.'))
          ->sortable(),
        Tables\Columns\TextInputColumn::make('sdt')
          ->label('SDT')
          ->sortable()
          ->summarize(Sum::make()->label('Total SDT')),
        Tables\Columns\TextColumn::make('nilai_sdt')
          ->label('Nilai SDT')
          ->state(function ($record): float {
            $record->update(['nilai_sdt' => $record->sdt * $record->sku->rbp]);
            return $record->nilai_sdt ?? 0;
          })
          ->toggleable(isToggledHiddenByDefault: false)
          ->prefix('Rp. ')
          ->numeric(locale: 'id')
          ->summarize(Sum::make()->label('Total Nilai SDT')->money('Rp.'))
          ->sortable(),
        Tables\Columns\TextInputColumn::make('sdp')
          ->label('SDP')
          ->sortable()
          ->summarize(Sum::make()->label('Total SDP')),
        Tables\Columns\TextColumn::make('nilai_sdp')
          ->label('Nilai SDP')
          ->state(function ($record): float {
            $record->update(['nilai_sdp' => $record->sdp * $record->sku->rbp]);
            return $record->nilai_sdp ?? 0;
          })
          ->toggleable(isToggledHiddenByDefault: false)
          ->prefix('Rp. ')
          ->numeric(locale: 'id')
          ->summarize(Sum::make()->label('Total Nilai SDP')->money('Rp.'))
          ->sortable(),
        Tables\Columns\TextColumn::make('sell_stock')
          ->label('SELL')
          ->state(function ($record): float {
            $record->update(['sell_stock' => $record->sdm + $record->sdt - $record->sdp]);
            return $record->sell_stock ?? 0;
          })
          ->sortable()
          ->summarize(Sum::make()->label('Total SELL')),
        Tables\Columns\TextColumn::make('nilai_sell_stock')
          ->label('Nilai SELL')
          ->state(function ($record): float {
            $record->update(['nilai_sell_stock' => $record->sell_stock * $record->sku->rbp]);
            PerencanaanPerjalananPermanentStock::find($record->pjp_stock_id)->update(['sell_stocks' => $record->where('pjp_stock_id', $record->pjp_stock_id)->sum('nilai_sell_stock')]);
            return $record->nilai_sell_stock ?? 0;
          })
          ->toggleable(isToggledHiddenByDefault: false)
          ->prefix('Rp. ')
          ->numeric(locale: 'id')
          ->summarize(Sum::make()->label('Total Nilai SELL')->money('Rp.'))
          ->sortable(),
      ])
      ->filters([
        Filter::make('created_at')
          ->form([
            DatePicker::make('Dari'),
            DatePicker::make('Sampai')
              ->default(now()),
          ])
          ->query(function (Builder $query, array $data): Builder {
            return $query
              ->when(
                $data['Dari'],
                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
              )
              ->when(
                $data['Sampai'],
                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
              );
          })
      ])
      ->actions([])
      ->bulkActions([]);
  }
  public function render()
  {
    return view('livewire.transaksi-stock');
  }
}
