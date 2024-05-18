<?php

namespace App\Livewire;

use App\Models\Absen;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
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
      ->query(Absen::query()->where('user_id', $this->userId))
      ->poll('10s')
      ->columns([
        TextColumn::make('user.karyawan.nama')
          ->sortable()
          ->searchable(),
        TextColumn::make('keterangan_absen')
          ->badge()
          ->color(fn (string $state): string => match ($state) {
            'Hadir' => 'success',
            'Alpa' => 'danger',
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
      ->actions([
        // ActionGroup::make([
        //   Tables\Actions\EditAction::make(),
        //   Tables\Actions\DeleteAction::make(),
        //   Tables\Actions\Action::make('Edit Status')
        //     ->hidden(function ($record) {
        //       if (Auth::user()->role === 'Leader' && $record->user->role === 'Leader') {
        //         return true;
        //       } elseif (Auth::user()->role === 'SE/SM' && $record->user->role === 'SE/SM') {
        //         return true;
        //       } elseif (Auth::user()->role === 'SPG' && $record->user->role === 'SPG') {
        //         return true;
        //       }
        //     })
        //     ->icon('heroicon-o-pencil')
        //     ->action(function (Absen $record, array $data): void {
        //       $record->status_absen = $data['status_absen'];
        //       $record->save();

        //       Notification::make()
        //         ->title('Status Absen Berhasil Diubah')
        //         ->success()
        //         ->send();
        //     })
        //     ->form([
        //       Select::make('status_absen')
        //         ->label('Status Absen')
        //         ->options([
        //           'Disetujui' => 'Setujui',
        //           'Ditolak' => 'Tolak',
        //         ])
        //         ->searchable()
        //         ->default(function (Absen $absen) {
        //           return $absen->status_absen;
        //         })
        //     ]),
        // ])
      ])
      ->bulkActions([
        // Tables\Actions\BulkActionGroup::make([]),
      ]);
  }
  public function render()
  {
    return view('livewire.detail-absen');
  }
}
