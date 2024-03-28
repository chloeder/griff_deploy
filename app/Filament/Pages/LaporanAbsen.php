<?php

namespace App\Filament\Pages;

use App\Models\Absen;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class LaporanAbsen extends Page implements HasTable
{
  use InteractsWithTable;
  protected static ?string $navigationIcon = 'heroicon-o-document-text';
  protected static ?int $navigationSort = 18;
  protected static ?string $navigationGroup = 'Laporan';
  protected static ?string $navigationLabel = 'Absensi';
  protected static string $view = 'filament.pages.laporan-absen';

  public static function canAccess(): bool
  {
    return auth()->user()->role === 'Admin' || auth()->user()->role === 'Leader';
  }

  public function table(Table $table): Table
  {
    config()->set('database.connections.mysql.strict', false);
    DB::reconnect();
    return $table
      ->modifyQueryUsing(function (Builder $query) {
        $query->join('users', 'users.id', '=', 'absens.user_id')->where('status_absen', 'Disetujui')->groupBy('absens.user_id');
      })
      ->poll('10s')
      ->poll('10s')
      ->query(Absen::query())
      ->columns([
        TextColumn::make('user.karyawan.nama')
          ->sortable()
          ->searchable(),
        TextColumn::make('user.username')
          ->label('Posisi')
          ->sortable()
          ->searchable(),
        TextColumn::make('masuk')
          ->label('Masuk')
          ->state(function (Absen $record): string {
            return $record->join('users', 'users.id', '=', 'absens.user_id')->where('users.id', $record->user_id)->where('status_absen', 'Disetujui')->where('keterangan_absen', 'Hadir')->whereMonth('tanggal_absen', now()->month)->whereYear('tanggal_absen', now()->year)->count();
          }),
        TextColumn::make('alpa')
          ->label('Alpa')
          ->state(function (Absen $record): string {
            return $record->join('users', 'users.id', '=', 'absens.user_id')->where('users.id', $record->user_id)->where('status_absen', 'Disetujui')->where('keterangan_absen', 'Alpa')->whereMonth('tanggal_absen', now()->month)->whereYear('tanggal_absen', now()->year)->count();
          }),
        TextColumn::make('izin')
          ->label('Izin')
          ->state(function (Absen $record): string {
            return $record->join('users', 'users.id', '=', 'absens.user_id')->where('users.id', $record->user_id)->where('status_absen', 'Disetujui')->where('keterangan_absen', 'Izin')->whereMonth('tanggal_absen', now()->month)->whereYear('tanggal_absen', now()->year)->count();
          }),
        TextColumn::make('sakit')
          ->label('Sakit')
          ->state(function (Absen $record): string {
            return $record->join('users', 'users.id', '=', 'absens.user_id')->where('users.id', $record->user_id)->where('status_absen', 'Disetujui')->where('keterangan_absen', 'Sakit')->whereMonth('tanggal_absen', now()->month)->whereYear('tanggal_absen', now()->year)->count();
          }),
        TextColumn::make('user.karyawan.no_rek')
          ->label('No. Rekening')
          ->sortable()
          ->searchable(),
        TextColumn::make('user.karyawan.bank')
          ->label('Bank')
          ->sortable()
          ->searchable(),
        TextColumn::make('user.karyawan.cabang')
          ->label('Cabang')
          ->sortable()
          ->searchable(),
        TextColumn::make('user.karyawan.an_nama')
          ->label('Atas Nama')
          ->sortable()
          ->searchable(),
      ])
      ->filters([
        Filter::make('tanggal_absen')
          ->form([
            DatePicker::make('Dari'),
            DatePicker::make('Sampai')
              ->default(now()),
          ])
          ->query(function (Builder $query, array $data): Builder {
            return $query
              ->when(
                $data['Dari'],
                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_absen', '>=', $date),
              )
              ->when(
                $data['Sampai'],
                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_absen', '<=', $date),
              );
          })
      ])
      ->actions([])
      ->bulkActions([
        ExportBulkAction::make(),
      ]);
  }
}
