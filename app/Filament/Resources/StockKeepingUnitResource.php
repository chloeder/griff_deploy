<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockKeepingUnitResource\Pages;
use App\Filament\Resources\StockKeepingUnitResource\RelationManagers;
use App\Models\StockKeepingUnit;
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

class StockKeepingUnitResource extends Resource
{
  protected static ?string $model = StockKeepingUnit::class;

  protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
  protected static ?string $navigationLabel = 'SKU';

  protected static ?string $navigationGroup = 'Master';
  protected static ?int $navigationSort = 5;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('Form Stock Keeping Unit')
          ->description('Form ini digunakan untuk menentukan stock keeping unit')
          ->schema([
            Forms\Components\TextInput::make('sku')
              ->label('SKU')
              ->unique()
              ->required()
              ->live(onBlur: true)
              ->afterStateUpdated(fn (Set $set, ?string $state) => $set('sku', strtoupper($state))),
            Forms\Components\TextInput::make('barcode')
              ->required()
              ->unique()
              ->minLength(12)
              ->numeric(),
            Forms\Components\TextInput::make('rbp')
              ->required()
              ->numeric()
              ->prefix('IDR'),
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
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('sku')
          ->label('SKU')
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('barcode')
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('rbp')
          ->numeric()
          ->money('IDR')
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
      'index' => Pages\ListStockKeepingUnits::route('/'),
      'create' => Pages\CreateStockKeepingUnit::route('/create'),
      'edit' => Pages\EditStockKeepingUnit::route('/{record:uuid}/edit'),
    ];
  }
}
