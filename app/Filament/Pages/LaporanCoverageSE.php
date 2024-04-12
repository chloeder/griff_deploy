<?php

namespace App\Filament\Pages;

use App\Models\PerencanaanPerjalananPermanent;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class LaporanCoverageSE extends Page implements HasTable
{
  use InteractsWithTable;
  protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';
  protected static ?string $navigationLabel = 'Coverage SE/SM';
  protected static ?string $title = 'Laporan Coverage SE/SM';
  protected static ?int $navigationSort = 16;
  protected static ?string $navigationGroup = 'Laporan';
  protected static string $view = 'filament.pages.laporan-coverage-s-e';
  public static function canAccess(): bool
  {
    return auth()->user()->role === 'Admin' || auth()->user()->role === 'Leader' || auth()->user()->role === 'SE/SM';
  }

  public function table(Table $table): Table
  {
    config()->set('database.connections.mysql.strict', false);
    DB::reconnect();
    return $table
      ->modifyQueryUsing(function (Builder $query) {
        $query->join('users', 'users.id', '=', 'perencanaan_perjalanan_permanents.sales_id')->join('tokos', 'tokos.id', '=', 'perencanaan_perjalanan_permanents.toko_id')->where('role', 'SE/SM')->groupBy('sales_id');
        // dd($data);
      })
      ->poll('10s')
      ->query(PerencanaanPerjalananPermanent::query())
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
        TextColumn::make('plan')
          ->label('PLAN')
          ->state(function ($record): string {
            $specificSalesId = $record->sales_id;
            return $record->join('users', 'users.id', '=', 'perencanaan_perjalanan_permanents.sales_id')
              ->where('perencanaan_perjalanan_permanents.sales_id', $specificSalesId)
              ->where(function ($query) {
                $query->where('pjp_status', 'PLAN')
                  ->orWhere('pjp_status', 'VISIT');
              })
              ->whereMonth('tanggal', now()->month)
              ->whereYear('tanggal', now()->year)
              ->count();

            // $record->where('pjp_status', 'PLAN')->orWhere('pjp_status', 'VISIT')->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year)->count();
            // dd($data);
          }),
        TextColumn::make('visit')
          ->label('VISIT')
          ->state(function (PerencanaanPerjalananPermanent $record): string {
            $specificSalesId = $record->sales_id;
            return $record->join('users', 'users.id', '=', 'perencanaan_perjalanan_permanents.sales_id')
              ->where('perencanaan_perjalanan_permanents.sales_id', $specificSalesId)
              ->where(function ($query) {
                $query->where('pjp_status', 'VISIT');
              })
              ->whereMonth('tanggal', now()->month)
              ->whereYear('tanggal', now()->year)
              ->count();
          }),
        TextColumn::make('ec')
          ->label('EC')
          ->state(function (PerencanaanPerjalananPermanent $record): string {
            $specificSalesId = $record->sales_id;
            return $record->join('users', 'users.id', '=', 'perencanaan_perjalanan_permanents.sales_id')
              ->where('perencanaan_perjalanan_permanents.sales_id', $specificSalesId)
              ->where('pjp_status', 'VISIT')
              ->whereMonth('tanggal', now()->month)
              ->whereYear('tanggal', now()->year)
              ->count();
          }),
        TextColumn::make('oa')
          ->label('OA')
          ->state(function (PerencanaanPerjalananPermanent $record): string {
            $specificSalesId = $record->sales_id;
            $data = $record->join('users', 'users.id', '=', 'perencanaan_perjalanan_permanents.sales_id')
              ->where('perencanaan_perjalanan_permanents.sales_id', $specificSalesId)
              ->where('role', 'SE/SM')
              ->where('pjp_status', 'VISIT')
              ->whereMonth('tanggal', now()->month)->count();
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
        ExportBulkAction::make(),
      ]);
  }
}
