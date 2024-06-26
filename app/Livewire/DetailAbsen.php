<?php

namespace App\Livewire;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use App\Models\Absen;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Livewire\Component;

class DetailAbsen extends Component implements HasTable, HasForms
{
  use InteractsWithTable;
  use InteractsWithForms;

  public $userId;

  public function mount($id)
  {
    $this->userId = $id;
  }

  public function table(Table $table): Table
  {
    return $table
      ->paginated([10, 25, 50, 100])
      ->groups([
        Group::make('keterangan_absen')
          ->label('KETERANGAN ABSEN'),
      ])
      ->defaultGroup('keterangan_absen')
      ->query(Absen::query()->where('user_id', $this->userId)->where('status_keluar', 'Disetujui'))
      ->poll('10s')
      ->columns([
        TextColumn::make('id')
          ->label('No')
          ->rowIndex()
          ->sortable(),
        TextColumn::make('user.karyawan.nama')
          ->sortable()
          ->searchable(),
        TextColumn::make('keterangan_absen')
          ->badge()
          ->color(fn (string $state): string => match ($state) {
            'Hadir' => 'success',
            'Izin' => 'warning',
            'Sakit' => 'info',
          })
          ->sortable()
          ->searchable(),
        TextColumn::make('tanggal_masuk')
          ->label('Tanggal Masuk')
          ->date()
          ->sortable()
          ->searchable(),
        TextColumn::make('waktu_masuk')
          ->label('Waktu Masuk')
          ->time()
          ->sortable()
          ->searchable(),
        TextColumn::make('lokasi_masuk')
          ->sortable()
          ->searchable(),
        TextColumn::make('tanggal_keluar')
          ->date()
          ->sortable(),
        TextColumn::make('waktu_keluar')
          ->label('Waktu Keluar')
          ->time()
          ->sortable()
          ->searchable(),
        TextColumn::make('lokasi_keluar')
          ->searchable()
          ->sortable(),
        TextColumn::make('status_absen')
          ->badge()
          ->color(fn (string $state): string => match ($state) {
            'Disetujui' => 'success',
            'Ditolak' => 'danger',
            'Proses' => 'warning',
          })
          ->default('Proses')
          ->searchable()
          ->sortable(),
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
        FilamentExportBulkAction::make('export')
          ->disableAdditionalColumns()
          ->defaultFormat('pdf')
          ->disableCsv()
      ]);
  }
  public function render()
  {
    return view('livewire.detail-absen');
  }
}
