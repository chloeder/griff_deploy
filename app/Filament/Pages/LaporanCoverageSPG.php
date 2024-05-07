<?php

namespace App\Filament\Pages;

use App\Models\PerencanaanPerjalananPermanent;
use App\Models\PerencanaanPerjalananPermanentStock;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class LaporanCoverageSPG extends Page implements HasTable
{
  use InteractsWithTable;
  protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
  protected static ?string $navigationLabel = 'Coverage SPG';
  protected static ?int $navigationSort = 17;
  protected static ?string $navigationGroup = 'Laporan';
  protected static string $view = 'filament.pages.laporan-coverage-s-p-g';
  public static function canAccess(): bool
  {
    return auth()->user()->role === 'Admin' || auth()->user()->role === 'Leader' || auth()->user()->role === 'SPG';
  }

  public function table(Table $table): Table
  {
    config()->set('database.connections.mysql.strict', false);
    DB::reconnect();
    return $table
      ->modifyQueryUsing(function (Builder $query) {
        $word = auth()->user()->username;
        $pieces = explode(' ', $word, 3);
        $lastWord = $pieces[0] . ' ' . $pieces[1];
        if (auth()->user()->role === 'Leader') {
          $query->leftJoin('users', 'users.id', '=', 'perencanaan_perjalanan_permanent_stocks.sales_id')->leftJoin('leaders', 'leaders.id', '=', 'perencanaan_perjalanan_permanent_stocks.leader_id')->where('role', 'SPG')->select('perencanaan_perjalanan_permanent_stocks.*', 'leaders.nama as leader')->where('leaders.nama', 'like', '%' . $lastWord . '%')->groupBy('sales_id')->get();
        } else {
          $query->join('users', 'users.id', '=', 'perencanaan_perjalanan_permanent_stocks.sales_id')->where('role', 'SPG')->groupBy('sales_id');
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
        TextColumn::make('plan')
          ->label('PLAN')
          ->state(function (PerencanaanPerjalananPermanentStock $record): string {
            $specificSalesId = $record->sales_id;
            return $record->join('users', 'users.id', '=', 'perencanaan_perjalanan_permanent_stocks.sales_id')
              ->where('perencanaan_perjalanan_permanent_stocks.sales_id', $specificSalesId)
              ->where('role', 'SPG')
              ->where(function ($query) {
                $query->where('pjp_status', 'PLAN')
                  ->orWhere('pjp_status', 'VISIT');
              })
              ->whereMonth('tanggal', now()->month)
              ->whereYear('tanggal', now()->year)
              ->count();
          }),
        TextColumn::make('visit')
          ->label('VISIT')
          ->state(function (PerencanaanPerjalananPermanentStock $record): string {
            $specificSalesId = $record->sales_id;
            return $record->join('users', 'users.id', '=', 'perencanaan_perjalanan_permanent_stocks.sales_id')
              ->where('perencanaan_perjalanan_permanent_stocks.sales_id', $specificSalesId)
              ->where('role', 'SPG')
              ->where('pjp_status', 'VISIT')
              ->whereMonth('tanggal', now()->month)
              ->whereYear('tanggal', now()->year)
              ->count();
          }),
        TextColumn::make('ec')
          ->label('EC')
          ->state(function (PerencanaanPerjalananPermanentStock $record): string {
            $specificSalesId = $record->sales_id;
            return $record->join('users', 'users.id', '=', 'perencanaan_perjalanan_permanent_stocks.sales_id')
              ->where('perencanaan_perjalanan_permanent_stocks.sales_id', $specificSalesId)
              ->where('role', 'SPG')
              ->where('pjp_status', 'VISIT')
              ->whereMonth('tanggal', now()->month)
              ->whereYear('tanggal', now()->year)
              ->count();
          }),
        TextColumn::make('oa')
          ->label('OA')
          ->state(function (PerencanaanPerjalananPermanentStock $record): string {
            $specificSalesId = $record->sales_id;
            $data = $record->join('users', 'users.id', '=', 'perencanaan_perjalanan_permanent_stocks.sales_id')
              ->where('perencanaan_perjalanan_permanent_stocks.sales_id', $specificSalesId)->where('role', 'SPG')
              ->where('pjp_status', 'VISIT')
              ->whereMonth('tanggal', now()->month)
              ->count();
            return $data >= 1 ? 1 : 0;
          }),
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
      ->actions([])
      ->bulkActions([
        // ExportBulkAction::make(),
      ]);
  }
}
