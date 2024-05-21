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
        // Section::make('Pilih Karyawan')
        //   ->description('Form ini dikhusukan untuk Admin')
        //   ->schema([
        //     Forms\Components\Select::make('user_id')
        //       ->relationship('user.karyawan', 'nama')
        //       ->searchable()
        //       ->preload()
        //       ->label('Karyawan')
        //       ->required(),
        //   ])->columns(1)->hidden(Auth::user()->role !== 'Admin'),
        Section::make('Absen Masuk')
          ->description('Form Absensi Masuk')
          ->schema([
            Forms\Components\Select::make('keterangan_absen')
              ->options([
                'Hadir' => 'Hadir',
                'Alpa' => 'Alpa',
                'Izin' => 'Izin',
                'Sakit' => 'Sakit',
              ])
              ->live()
              ->afterStateUpdated(function (Set $set) {
                $set('lokasi_masuk', null);
              })
              ->searchable(fn (Get $get) => $get('keterangan_absen') === 'Alpa' || $get('keterangan_absen') === 'Sakit' || $get('keterangan_absen') === 'Izin' || $get('status_absen') === 'Disetujui')
              ->disabled(fn (Get $get) => $get('keterangan_absen') === 'Alpa' || $get('keterangan_absen') === 'Sakit' || $get('keterangan_absen') === 'Izin' || $get('status_absen') === 'Disetujui')
              ->required(),
            Forms\Components\Select::make('lokasi_masuk')
              ->options([
                'Kantor' => 'Kantor',
                'Distributor' => 'Distributor',
                'Toko' => 'Toko',
              ])
              ->live()
              ->searchable(fn (Get $get) => $get('keterangan_absen') === 'Alpa' || $get('keterangan_absen') === 'Sakit' || $get('keterangan_absen') === 'Izin' || $get('status_absen') === 'Disetujui')
              ->disabled(fn (Get $get) => $get('keterangan_absen') === 'Alpa' || $get('keterangan_absen') === 'Sakit' || $get('keterangan_absen') === 'Izin' || $get('status_absen') === 'Disetujui'),
          ])
          ->columns(2)->collapsible(),
        Section::make('Absen Keluar')
          ->description('Form Absensi Keluar')
          ->schema([
            Forms\Components\Select::make('lokasi_keluar')
              ->options([
                'Kantor' => 'Kantor',
                'Distributor' => 'Distributor',
                'Toko' => 'Toko',
              ])
              ->searchable(),
          ])
          ->columns(1)
          ->collapsible()
          ->hidden(fn (Get $get) => $get('status_absen') !== 'Disetujui'),
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
          $data = $query->select('absens.*', 'users.username as username')->join('users', 'users.id', '=', 'absens.user_id')->where('users.username', 'like', '%' . $lastWord . '%')->get();
          // dd($data->toArray());
          // dd($lastWord);
        }
      })
      ->poll('10s')
      ->columns([
        Tables\Columns\TextColumn::make('user.karyawan.nama')
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('keterangan_absen')
          ->badge()
          ->color(fn (string $state): string => match ($state) {
            'Hadir' => 'success',
            'Alpa' => 'danger',
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
        Tables\Columns\TextColumn::make('status_absen')
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
            ->hidden(function ($record) {
              if (Auth::user()->role === 'Leader' && $record->user->role === 'Leader') {
                if ($record->lokasi_keluar != null) {
                  return true;
                }
              }
            }),
          Tables\Actions\DeleteAction::make()
            ->hidden(function ($record) {
              if (Auth::user()->role === 'Leader' && $record->user->role === 'Leader') {
                return true;
              }
            }),
          Tables\Actions\Action::make('Edit Status')
            ->hidden(function ($record) {
              if (Auth::user()->role === 'Leader' && $record->user->role === 'Leader') {
                return true;
              } elseif (Auth::user()->role === 'SE/SM' && $record->user->role === 'SE/SM') {
                return true;
              } elseif (Auth::user()->role === 'SPG' && $record->user->role === 'SPG') {
                return true;
              }
            })
            ->icon('heroicon-o-pencil')
            ->action(function (Absen $record, array $data): void {
              $record->status_absen = $data['status_absen'];
              $record->save();

              Notification::make()
                ->title('Status Absen Berhasil Diubah')
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
