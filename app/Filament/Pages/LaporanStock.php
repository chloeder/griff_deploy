<?php

namespace App\Filament\Pages;

use App\Models\PerencanaanPerjalananPermanentStock;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class LaporanStock extends Page implements HasTable
{
  use InteractsWithTable;
  protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
  protected static ?string $navigationLabel = 'Stock';
  protected static ?int $navigationSort = 15;
  protected static ?string $navigationGroup = 'Laporan';
  protected static string $view = 'filament.pages.laporan-stock';
  public static function canAccess(): bool
  {
    return auth()->user()->role === 'Admin' || auth()->user()->role === 'Leader' || auth()->user()->role === 'SPG';
  }

  public function table(Table $table): Table
  {
    return $table
      ->modifyQueryUsing(function (Builder $query) {
        if (auth()->user()->role === 'SPG') {
          $query->where('sales_id', auth()->user()->id)->where('pjp_status', 'VISIT');
        } elseif (auth()->user()->role === 'Leader') {
          $word = auth()->user()->username;
          $pieces = explode(' ', $word, 2);
          $lastWord = end($pieces);
          $query->leftJoin('leaders', 'leaders.id', '=', 'perencanaan_perjalanan_permanent_stocks.leader_id')->select('perencanaan_perjalanan_permanent_stocks.*', 'leaders.nama as leader')->where('pjp_status', 'VISIT')->where('leaders.nama', 'like', '%' . $lastWord . '%')->get();
        } else {
          $query->where('pjp_status', 'VISIT');
        }
      })
      ->poll('10s')
      ->query(PerencanaanPerjalananPermanentStock::query())
      ->columns([
        TextColumn::make('leader.nama')
          ->searchable()
          ->sortable(),
        TextColumn::make('klaster.nama')
          ->searchable()
          ->sortable(),
        TextColumn::make('sub_klaster.nama')
          ->searchable()
          ->sortable(),
        TextColumn::make('sales.user.username')
          ->label('Sales')
          ->searchable()
          ->sortable(),
        TextColumn::make('toko.nama')
          ->searchable()
          ->sortable(),
        TextColumn::make('toko.tipe_toko')
          ->label('Tipe Toko')
          ->searchable()
          ->sortable(),
        TextColumn::make('pjp_status')
          ->badge()
          ->color(fn (string $state): string => match ($state) {
            'PLAN' => 'info',
            'VISIT' => 'info',
          })
          ->label('PJP Status')
          ->searchable()
          ->sortable(),
        TextColumn::make('stock_sum_nilai_sdm')
          ->badge()->color('success')
          ->prefix('Rp. ')
          ->numeric(locale: 'id')
          ->label('SDM')
          ->sum('stock', 'nilai_sdm')
          ->sortable(),
        TextColumn::make('stock_sum_nilai_sdt')
          ->badge()->color('success')
          ->numeric(locale: 'id')
          ->prefix('Rp. ')
          ->label('SDT')
          ->sum('stock', 'nilai_sdt')
          ->sortable(),
        TextColumn::make('stock_sum_nilai_sdp')
          ->badge()->color('success')
          ->prefix('Rp. ')
          ->numeric(locale: 'id')
          ->label('SDP')
          ->sum('stock', 'nilai_sdp')
          ->sortable(),
        TextColumn::make('stock_sum_nilai_sell_stock')
          ->badge()->color('success')
          ->prefix('Rp. ')
          ->numeric(locale: 'id')
          ->label('SELL')
          ->sum('stock', 'nilai_sell_stock')
          ->sortable(),
      ])
      ->filters([
        Filter::make('tanggal')
          ->form([
            DatePicker::make('Dari'),
            DatePicker::make('Sampai')
              ->default(now()),
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
      ->actions(
        [
          Action::make('Lihat')
            ->hidden(Auth::user()->role !== 'Admin' && Auth::user()->role !== 'Leader')
            ->url(fn (PerencanaanPerjalananPermanentStock $record): string => route('transaksi-stock', ['id' => $record->id]))
            ->icon('heroicon-o-eye')
        ]
      )
      ->bulkActions([]);
  }
}
