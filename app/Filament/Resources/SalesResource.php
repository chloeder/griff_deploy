<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesResource\Pages;
use App\Filament\Resources\SalesResource\RelationManagers;
use App\Models\Klaster;
use App\Models\Sales;
use App\Models\SubKlaster;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class SalesResource extends Resource
{
  protected static ?string $model = Sales::class;

  protected static ?string $navigationIcon = 'heroicon-o-user-group';
  protected static ?string $navigationGroup = 'Master';
  protected static ?int $navigationSort = 4;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('Form Sales')
          ->description('Form ini dikhususkan untuk menentukan sales dari setiap wilayah')
          ->schema([
            Forms\Components\Select::make('leader_id')
              ->relationship('leader', 'nama')
              ->searchable()
              ->preload()
              ->live()
              ->afterStateUpdated(function (Set $set) {
                $set('klaster_id', null);
                $set('sub_klaster_id', null);
              })
              ->required(),
            Forms\Components\Select::make('klaster_id')
              ->label('Klaster')
              ->options(fn (Get $get): Collection => Klaster::query()
                ->where('leader_id', $get('leader_id'))
                ->pluck('nama', 'id'))
              ->live()
              ->afterStateUpdated(function (Set $set) {
                $set('sub_klaster_id', null);
              })
              ->searchable()
              ->required(),
            Forms\Components\Select::make('sub_klaster_id')
              ->label('Sub Klaster')
              ->options(fn (Get $get): Collection => SubKlaster::query()
                ->where('klaster_id', $get('klaster_id'))
                ->pluck('nama', 'id'))
              ->searchable()
              ->required(),
            Forms\Components\Select::make('user_id')
              ->label('Sales')
              ->relationship('user', 'username', function (Builder $query) {
                return $query->where('data_karyawan_id', '!=', null)->where('role', 'se/sm')->orWhere('role', 'spg');
              })
              ->searchable()
              ->preload()
              ->required(),
          ])
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
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('leader.nama')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('klaster.nama')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('sub_klaster.nama')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('user.username')
          ->label('Posisi')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('user.karyawan.nama')
          ->searchable()
          ->sortable(),
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
      'index' => Pages\ListSales::route('/'),
      'create' => Pages\CreateSales::route('/create'),
      'edit' => Pages\EditSales::route('/{record:uuid}/edit'),
    ];
  }
}
