<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KlasterResource\Pages;
use App\Filament\Resources\KlasterResource\RelationManagers;
use App\Models\Klaster;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KlasterResource extends Resource
{
  protected static ?string $model = Klaster::class;

  protected static ?string $navigationIcon = 'heroicon-o-building-office';
  protected static ?string $navigationGroup = 'Master';
  protected static ?int $navigationSort = 2;
  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('Form Pembuatan Klaster')
          ->description('Form ini digunakan untuk menentukan wilayah yang akan digunakan untuk pembuatan klaster')
          ->schema([
            Forms\Components\Select::make('leader_id')
              ->required()
              ->relationship('leader', 'nama')
              ->searchable()
              ->preload(),
            Forms\Components\TextInput::make('area')
              ->required()
              ->live(onBlur: true)
              ->afterStateUpdated(fn (Set $set, ?string $state) => $set('area', strtoupper($state))),
            Forms\Components\TextInput::make('nama')
              ->label('Klaster')
              ->unique()
              ->required()
              ->live(onBlur: true)
              ->afterStateUpdated(fn (Set $set, ?string $state) => $set('nama', strtoupper($state))),
          ]),
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
          ->searchable(),
        Tables\Columns\TextColumn::make('leader.nama')
          ->searchable(),
        Tables\Columns\TextColumn::make('area')
          ->searchable(),
        Tables\Columns\TextColumn::make('nama')
          ->label('Klaster')
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
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
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
      'index' => Pages\ListKlasters::route('/'),
      'create' => Pages\CreateKlaster::route('/create'),
      'edit' => Pages\EditKlaster::route('/{record:uuid}/edit'),
    ];
  }
}
