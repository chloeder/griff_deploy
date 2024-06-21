<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsenResource\Pages;
use App\Filament\Resources\AbsenResource\RelationManagers;
use App\Models\Absen;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class AbsenResource extends Resource
{
  protected static ?string $model = Absen::class;

  protected static ?string $navigationIcon = 'heroicon-o-qr-code';
  protected static ?string $navigationGroup = 'Master';
  protected static ?int $navigationSort = 7;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('Absen Masuk')
          ->description('Form Absensi Masuk')
          ->schema([
            Forms\Components\Select::make('keterangan_absen')
              ->options([
                'Hadir' => 'Hadir',
                'Izin' => 'Izin',
                'Sakit' => 'Sakit',
              ])
              ->live()
              ->afterStateUpdated(function (Set $set) {
                $set('lokasi_masuk', null);
              })
              ->searchable(fn (Get $get) => $get('keterangan_absen') === 'Sakit' || $get('keterangan_absen') === 'Izin' || $get('status_absen') === 'Disetujui')
              // ->disabled(fn (Get $get) => $get('keterangan_absen') === 'Sakit' || $get('keterangan_absen') === 'Izin' || $get('status_absen') === 'Disetujui')
              ->required(),
            Forms\Components\Select::make('lokasi_masuk')
              ->options([
                'Kantor' => 'Kantor',
                'Distributor' => 'Distributor',
                'Toko' => 'Toko',
              ])
              ->live()
              ->searchable(fn (Get $get) => $get('keterangan_absen') === 'Sakit' || $get('keterangan_absen') === 'Izin' || $get('status_absen') === 'Disetujui')
              ->disabled(fn (Get $get) => $get('keterangan_absen') === 'Sakit' || $get('keterangan_absen') === 'Izin' || $get('status_absen') === 'Disetujui'),
          ])
          ->columns(2)->collapsible(),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->recordUrl(null)
      ->modifyQueryUsing(function (Builder $query) {
        if (auth()->user()->role === 'SE/SM' || auth()->user()->role === 'SPG') {
          $query->where('user_id', auth()->user()->id);
        } elseif (auth()->user()->role === 'Leader') {
          $word = auth()->user()->username;
          $pieces = explode(' ', $word, 3);
          $lastWord = $pieces[0] . ' ' . $pieces[1];
          $data = $query->select('absens.*', 'users.username')->join('users', 'users.id', '=', 'absens.user_id')->where('users.username', 'like', '%' . $lastWord . '%')->get();
          // dd($lastWord);
          // dd($data->toArray());
        } else {
          $query->select('absens.*', 'users.username')->join('users', 'users.id', '=', 'absens.user_id')->get();
        }
      })
      ->poll('10s')
      ->columns([
        Tables\Columns\TextColumn::make('user.karyawan.nama')
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('username')
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('keterangan_absen')
          ->badge()
          ->color(fn (string $state): string => match ($state) {
            'Hadir' => 'success',
            'Izin' => 'warning',
            'Sakit' => 'info',
          })
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('tanggal_masuk')
          ->label('Tanggal Masuk')
          ->date()
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('waktu_masuk')
          ->label('Waktu Masuk')
          ->time()
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('lokasi_masuk')
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('status_absen')
          ->label('Status Absen Masuk')
          ->badge()
          ->color(fn (string $state): string => match ($state) {
            'Disetujui' => 'success',
            'Ditolak' => 'danger',
            'Proses' => 'warning',
          })
          ->default('Proses')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('tanggal_keluar')
          ->date()
          ->sortable(),
        Tables\Columns\TextColumn::make('waktu_keluar')
          ->label('Waktu Keluar')
          ->time()
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('lokasi_keluar')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('status_keluar')
          ->label('Status Absen Keluar')
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
              ->default(now()),
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
        ActionGroup::make([
          Tables\Actions\EditAction::make()
            ->label('Ubah Absen Masuk')
            ->hidden(function ($record) {
              if ($record->status_absen === 'Disetujui') {
                return true;
              }
            }),
          Tables\Actions\Action::make('Edit Status Masuk')
            ->hidden(function ($record) {
              if (Auth::user()->role === 'Leader' && $record->user->role === 'Leader') {
                return true;
              } elseif (Auth::user()->role === 'SE/SM' && $record->user->role === 'SE/SM') {
                return true;
              } elseif (Auth::user()->role === 'SPG' && $record->user->role === 'SPG') {
                return true;
              } elseif ($record->status_absen == 'Disetujui') {
                return true;
              }
            })
            ->icon('heroicon-o-pencil')
            ->action(function (Absen $record, array $data): void {
              $record->status_absen = $data['status_absen'];
              $record->save();

              Notification::make()
                ->title('Status Absen Masuk Berhasil Diubah')
                ->success()
                ->send();
            })
            ->form([
              Select::make('status_absen')
                ->label('Status Absen')
                ->options([
                  'Disetujui' => 'Setujui',
                  'Ditolak' => 'Tolak',
                ])
                ->required()
                ->searchable()
                ->default(function (Absen $absen) {
                  return $absen->status_absen;
                })
            ]),
          Tables\Actions\Action::make('Absen Keluar')
            ->label('Absen Keluar')
            ->icon('heroicon-o-pencil-square')
            ->action(function (Absen $record, array $data): void {
              if ($record->tanggal_keluar === null && $record->waktu_keluar === null) {
                session(['tanggal_keluar' => Carbon::now()->format('Y-m-d')]);
                session(['waktu_keluar' => Carbon::now()->format('H:i:s')]);
              }

              if ($record->status_keluar === 'Proses' || $record->status_keluar === null) {
                $record->tanggal_keluar = session('tanggal_keluar');
                $record->waktu_keluar = session('waktu_keluar');
                $record->lokasi_keluar = $data['lokasi_keluar'];
                $record->save();
              }

              if ($record->status_keluar === 'Ditolak') {
                $record->lokasi_keluar = $data['lokasi_keluar'];
                $record->status_keluar = 'Proses';
                $record->save();
              }

              if ($record->status_keluar === 'Disetujui') {
                $record->lokasi_keluar = $data['lokasi_keluar'];
                $record->save();
              }

              Notification::make()
                ->title('Absen Keluar Sukses')
                ->success()
                ->send();
            })
            ->form([
              Select::make('lokasi_keluar')
                ->label('Lokasi Keluar')
                ->options([
                  'Kantor' => 'Kantor',
                  'Distributor' => 'Distributor',
                  'Toko' => 'Toko',
                ])
                ->required()
                ->searchable()
                ->default(function (Absen $absen) {
                  return $absen->lokasi_keluar;
                })
            ])
            ->hidden(function ($record) {
              if ($record->status_absen === null || $record->status_absen === 'Proses' || $record->status_absen === 'Ditolak') {
                return true;
              }
            }),
          Tables\Actions\Action::make('Edit Status Keluar')
            ->hidden(function ($record) {
              if (Auth::user()->role === 'Leader' && $record->user->role === 'Leader') {
                return true;
              } elseif (Auth::user()->role === 'SE/SM' && $record->user->role === 'SE/SM') {
                return true;
              } elseif (Auth::user()->role === 'SPG' && $record->user->role === 'SPG') {
                return true;
              } elseif ($record->status_absen !== 'Disetujui') {
                return true;
              }
            })
            ->icon('heroicon-o-pencil')
            ->action(function (Absen $record, array $data): void {
              $record->status_keluar = $data['status_keluar'];
              $record->save();

              Notification::make()
                ->title('Status Absen Keluar Berhasil Diubah')
                ->success()
                ->send();
            })
            ->form([
              Select::make('status_keluar')
                ->label('Status Absen')
                ->options([
                  'Disetujui' => 'Setujui',
                  'Ditolak' => 'Tolak',
                ])
                ->required()
                ->searchable()
                ->default(function (Absen $absen) {
                  return $absen->status_absen;
                })
            ]),
          Tables\Actions\DeleteAction::make()
            ->hidden(function ($record) {
              if (Auth::user()->role === 'Leader' && $record->user->role === 'Leader') {
                return true;
              }
            }),
        ])
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([]),
      ]);
  }

  public static function getRelations(): array
  {
    return [
      //
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListAbsens::route('/'),
      'create' => Pages\CreateAbsen::route('/create'),
      'edit' => Pages\EditAbsen::route('/{record:uuid}/edit'),
    ];
  }
}
