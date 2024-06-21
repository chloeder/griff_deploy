<?php

namespace App\Livewire;

use App\Models\PerencanaanPerjalananPermanentStock;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DetailCoverageSPG extends Component implements HasTable, HasForms
{
  use InteractsWithTable;
  use InteractsWithForms;

  public $salesId;

  public function mount($id)
  {
    $this->salesId = $id;
  }

  public function table(Table $table): Table
  {
    return $table
      ->groups([
        Group::make('status')
          ->label('STATUS'),
        Group::make('pjp_status')
          ->label('PJP STATUS'),
      ])
      ->defaultGroup('pjp_status')
      ->query(PerencanaanPerjalananPermanentStock::query()->where('sales_id', $this->salesId))
      ->poll('10s')
      ->columns([
        TextColumn::make('id')
          ->label('No')
          ->rowIndex()
          ->searchable()
          ->sortable(),
        TextColumn::make('leader.nama')
          ->toggleable(isToggledHiddenByDefault: true)
          ->searchable()
          ->sortable(),
        TextColumn::make('klaster.nama')
          ->toggleable(isToggledHiddenByDefault: true)
          ->searchable()
          ->sortable(),
        TextColumn::make('sub_klaster.nama')
          ->toggleable(isToggledHiddenByDefault: true)
          ->searchable()
          ->sortable(),
        TextColumn::make('sales.user.username')
          ->toggleable(isToggledHiddenByDefault: false)
          ->label('Sales')
          ->searchable()
          ->sortable(),
        TextColumn::make('toko.nama_toko')
          ->label('Toko')
          ->toggleable(isToggledHiddenByDefault: false)
          ->searchable()
          ->sortable(),
        TextColumn::make('toko.tipe_toko')
          ->label('Tipe Toko')
          ->toggleable(isToggledHiddenByDefault: false)
          ->searchable()
          ->sortable(),
        TextColumn::make('tanggal')
          ->toggleable(isToggledHiddenByDefault: false)
          ->searchable()
          ->sortable(),
        TextColumn::make('stock_sum_nilai_sdm')

          // ->url(fn (PerencanaanPerjalananPermanentStock $record): string => route('transaksi-stock', ['id' => $record->id]))
          ->badge()
          ->color('success')
          ->label('SDM')
          ->sum('stock', 'nilai_sdm')
          ->prefix('Rp. ')
          ->numeric(locale: 'id')->sortable(),
        TextColumn::make('stock_sum_nilai_sdt')

          // ->url(fn (PerencanaanPerjalananPermanentStock $record): string => route('transaksi-stock', ['id' => $record->id]))
          ->badge()
          ->color('success')
          ->label('SDT')
          ->sum('stock', 'nilai_sdt')
          ->prefix('Rp. ')
          ->numeric(locale: 'id')->sortable(),
        TextColumn::make('stock_sum_nilai_sdp')

          // ->url(fn (PerencanaanPerjalananPermanentStock $record): string => route('transaksi-stock', ['id' => $record->id]))
          ->badge()
          ->color('success')
          ->label('SDP')
          ->sum('stock', 'nilai_sdp')
          ->prefix('Rp. ')
          ->numeric(locale: 'id')->sortable(),
        TextColumn::make('stock_sum_nilai_sell_stock')

          ->badge()
          ->color('success')
          ->label('SELL STOCK')
          ->sum('stock', 'nilai_sell_stock')
          ->prefix('Rp. ')
          ->numeric(locale: 'id')->sortable(),
        TextColumn::make('pjp_status')
          ->badge()
          ->color(fn (string $state): string => match ($state) {
            'PLAN' => 'info',
            'VISIT' => 'success',
          })
          ->label('PJP Status')
          ->searchable()
          ->sortable(),
        TextColumn::make('status')
          ->badge()
          ->color(fn (string $state): string => match ($state) {
            'Pending' => 'warning',
            'Disetujui' => 'success',
            'Ditolak' => 'danger',
          })
          ->label('Status')
          ->searchable()
          ->sortable(),

      ])
      ->filters([
        Filter::make('tanggal')
          ->form([
            DatePicker::make('Dari')
              ->default(now()->startOfMonth()),
            DatePicker::make('Sampai')
              ->default(now()->endOfMonth()),
          ])
          ->query(function (Builder $query, array $data): Builder {
            return $query
              ->when(
                $data['Dari'],
                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
              )
              ->when(
                $data['Sampai'],
                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
              );
          })
      ])
      ->actions([])
      ->bulkActions([]);
  }

  public function render()
  {
    return view('livewire.detail-coverage-s-p-g');
  }
}
