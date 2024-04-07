<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaderResource\Pages;
use App\Filament\Resources\LeaderResource\RelationManagers;
use App\Models\Leader;
use App\Models\User;
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

class LeaderResource extends Resource
{
  protected static ?string $model = Leader::class;

  protected static ?string $navigationIcon = 'heroicon-o-user';
  protected static ?string $navigationGroup = 'Master';
  protected static ?int $navigationSort = 1;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('Form Pembuatan Leader')
          ->description('Form ini dikhususkan untuk menentukan leader dari setiap wilayah')
          ->schema([
            Forms\Components\TextInput::make('nama')
              ->label('Leader')
              ->unique()
              ->required()
              ->live(onBlur: true)
              ->afterStateUpdated(fn (Set $set, ?string $state) => $set('nama', strtoupper($state))),
            Forms\Components\Select::make('user_id')
              ->label('Karyawan')
              ->relationship('user', 'id', function (Builder $query) {
                return $query->where('role', 'leader')->where('data_karyawan_id', '!=', null);
              })
              ->getOptionLabelFromRecordUsing(fn (User $record) => $record->karyawan->nama)
              ->searchable()
              ->preload()
              ->required(),
          ]),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('id')
          ->label('No')
          ->rowIndex()
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('nama')
          ->label('Leader')
          ->searchable()->sortable(),
        Tables\Columns\TextColumn::make('user.karyawan.nama')
          ->searchable()->sortable(),
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
    return [];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListLeaders::route('/'),
      'create' => Pages\CreateLeader::route('/create'),
      'edit' => Pages\EditLeader::route('/{record:uuid}/edit'),
    ];
  }
}
