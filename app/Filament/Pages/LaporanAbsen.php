<?php

namespace App\Filament\Pages;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use App\Models\Absen;
use Filament\Tables\Actions\Action;
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
      ->paginated([10, 25, 50, 100])
      ->modifyQueryUsing(function (Builder $query) {
        if (auth()->user()->role === 'Leader') {
          $word = auth()->user()->username;
          $pieces = explode(' ', $word, 3);
          $lastWord = $pieces[0] . ' ' . $pieces[1];
          $query->select('absens.*')->join('users', 'users.id', '=', 'absens.user_id')
            ->where('status_absen', 'Disetujui')
            ->where('users.username', 'like', '%' . $lastWord . '%')
            ->groupBy('absens.user_id')
            ->orderBy('users.username', 'asc');
        } else {
          $query->select('absens.*')->join('users', 'users.id', '=', 'absens.user_id')
            ->where('status_absen', 'Disetujui')
            ->groupBy('absens.user_id');
        }
      })
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
            $dari = session('Dari');
            $sampai = session('Sampai');
            return $record->join('users', 'users.id', '=', 'absens.user_id')
              ->where('users.id', $record->user_id)
              ->where('status_keluar', 'Disetujui')
              ->where('keterangan_absen', 'Hadir')
              ->where('status_keluar', 'Disetujui')
              ->whereBetween('absens.tanggal_absen', [$dari, $sampai])
              ->count();
          }),
        TextColumn::make('alpa')
          ->label('Alpa')
          ->state(function (Absen $record): string {
            $dari = session('Dari');
            $sampai = session('Sampai');
            return $record->join('users', 'users.id', '=', 'absens.user_id')
              ->where('users.id', $record->user_id)
              ->where('status_absen', 'Disetujui')
              ->where('keterangan_absen', 'Alpa')
              ->whereBetween('absens.tanggal_absen', [$dari, $sampai])
              ->count();
          }),
        TextColumn::make('izin')
          ->label('Izin')
          ->state(function (Absen $record): string {
            $dari = session('Dari');
            $sampai = session('Sampai');
            return $record->join('users', 'users.id', '=', 'absens.user_id')
              ->where('users.id', $record->user_id)
              ->where('status_absen', 'Disetujui')
              ->where('keterangan_absen', 'Izin')
              ->whereBetween('absens.tanggal_absen', [$dari, $sampai])
              ->count();
          }),
        TextColumn::make('sakit')
          ->label('Sakit')
          ->state(function (Absen $record): string {
            $dari = session('Dari');
            $sampai = session('Sampai');
            return $record->join('users', 'users.id', '=', 'absens.user_id')
              ->where('users.id', $record->user_id)
              ->where('status_absen', 'Disetujui')
              ->where('keterangan_absen', 'Sakit')
              ->whereBetween('absens.tanggal_absen', [$dari, $sampai])
              ->count();
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
            DatePicker::make('Dari')
              ->default(now()->startOfMonth()),

            DatePicker::make('Sampai')
              ->default(now()->endOfMonth()),
          ])
          ->query(function (Builder $query, array $data): Builder {
            // Store the selected date range in the session
            session(['Dari' => $data['Dari']]);
            session(['Sampai' => $data['Sampai']]);

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
      ->actions([
        Action::make('Lihat')
          ->hidden(Auth::user()->role !== 'Admin' && Auth::user()->role !== 'Leader')
          ->url(fn (Absen $record): string => route('detail-absen', ['id' => $record->user_id]))
          ->icon('heroicon-o-eye')
      ])
      ->bulkActions(
        [
          FilamentExportBulkAction::make('Export')
            ->disableAdditionalColumns()
            ->defaultFormat('pdf')
            ->disableCsv()
        ]
      );
  }
}
