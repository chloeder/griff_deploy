<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubKlasterResource\Pages;
use App\Filament\Resources\SubKlasterResource\RelationManagers;
use App\Models\Klaster;
use App\Models\SubKlaster;
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

class SubKlasterResource extends Resource
{
  protected static ?string $model = SubKlaster::class;

  protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
  protected static ?string $navigationGroup = 'Master';
  protected static ?int $navigationSort = 3;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('Form Sub Klaster')
          ->description('Form ini dikhususkan untuk menentukan sub klaster dari setiap wilayah')
          ->schema([
            Forms\Components\Select::make('leader_id')
              ->required()
              ->relationship('leader', 'nama')
              ->searchable()
              ->preload()
              ->live()
              ->afterStateUpdated(fn (Set $set) => $set('klaster_id', null)),
            Forms\Components\Select::make('klaster_id')
              ->label('Klaster')
              ->required()
              ->options(fn (Get $get): Collection => Klaster::query()
                ->where('leader_id', $get('leader_id'))
                ->pluck('nama', 'id'))
              ->searchable()
              ->preload(),
            Forms\Components\TextInput::make('nama')
              ->label('Sub Klaster')
              ->required()
              ->unique()
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
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('leader.nama')
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('klaster.nama')
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('nama')
          ->label('Sub Klaster')
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
      'index' => Pages\ListSubKlasters::route('/'),
      'create' => Pages\CreateSubKlaster::route('/create'),
      'edit' => Pages\EditSubKlaster::route('/{record:uuid}/edit'),
    ];
  }
}
