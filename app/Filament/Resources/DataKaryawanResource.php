<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataKaryawanResource\Pages;
use App\Filament\Resources\DataKaryawanResource\RelationManagers;
use App\Models\DataKaryawan;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DataKaryawanResource extends Resource
{
  protected static ?string $model = DataKaryawan::class;

  protected static ?string $navigationIcon = 'heroicon-o-identification';
  protected static ?string $navigationGroup = 'Master';
  protected static ?int $navigationSort = 8;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('Form Data Diri Karyawan')
          ->description('Form ini dikhususkan untuk menentukan data diri karyawan')
          ->schema([
            Forms\Components\TextInput::make('nama')
              ->required()
              ->live(onBlur: true)
              ->afterStateUpdated(fn (Set $set, ?string $state) => $set('nama', strtoupper($state)))
              ->maxLength(255),
            Forms\Components\TextInput::make('no_rek')
              ->unique()
              ->numeric()
              ->maxLength(255),
            Forms\Components\TextInput::make('bank')
              ->live(onBlur: true)
              ->afterStateUpdated(fn (Set $set, ?string $state) => $set('bank', strtoupper($state)))
              ->maxLength(255),
            Forms\Components\TextInput::make('cabang')
              ->live(onBlur: true)
              ->afterStateUpdated(fn (Set $set, ?string $state) => $set('cabang', strtoupper($state)))
              ->maxLength(255),
            Forms\Components\TextInput::make('an_nama')
              ->live(onBlur: true)
              ->afterStateUpdated(fn (Set $set, ?string $state) => $set('an_nama', strtoupper($state)))
              ->maxLength(255),
          ])
          ->columns(1)->columnSpan(1),
        Section::make('Pilih Status Karyawan')
          ->schema([
            ToggleButtons::make('status')
              ->options([
                'Aktif' => 'Aktifkan',
                'Non-Aktif' => 'Non Aktifkan',
              ])
              ->colors([
                'Aktif' => 'success',
                'Non-Aktif' => 'danger',
              ])
              ->icons([
                'Aktif' => 'heroicon-o-check',
                'Non-Aktif' => 'heroicon-o-x-mark',
              ])
              ->inline()
              ->required()
              ->live()
              ->afterStateUpdated(function (Set $set) {
                $set('tanggal_aktif', Carbon::now()->format('d F Y'));
              }),
            Forms\Components\TextInput::make('tanggal_aktif')
              ->readOnly(),
          ])->columns(1)->columnSpan(1),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->recordUrl(null)
      ->columns([
        Tables\Columns\TextColumn::make('id')
          ->label('No')
          ->rowIndex()
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('nama')
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('no_rek')
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('bank')
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('cabang')
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('an_nama')
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('status')
          ->sortable()
          ->searchable()
          ->badge()
          ->color(fn (string $state): string => match ($state) {
            'Aktif' => 'success',
            'Non-Aktif' => 'danger',
          }),
        Tables\Columns\TextColumn::make('tanggal_aktif')
          ->sortable()
          ->searchable(),
      ])
      ->filters([
        //
      ])
      ->actions([
        ActionGroup::make([
          Tables\Actions\EditAction::make(),
          Tables\Actions\DeleteAction::make(),
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
      'index' => Pages\ListDataKaryawans::route('/'),
      'create' => Pages\CreateDataKaryawan::route('/create'),
      'edit' => Pages\EditDataKaryawan::route('/{record}/edit'),
    ];
  }
}
