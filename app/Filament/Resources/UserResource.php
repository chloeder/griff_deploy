<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
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
use Illuminate\Contracts\Support\Htmlable;


class UserResource extends Resource
{
  protected static ?string $model = User::class;
  protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
  protected static ?string $navigationGroup = 'Master';
  protected static ?int $navigationSort = 9;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('Akun Pengguna')
          ->description('Form ini digunakan untuk membuat akun yang akan digunakan untuk login ke aplikasi.')
          ->schema([
            Forms\Components\TextInput::make('username')
              ->required()
              ->minLength(7)
              ->live(onBlur: true)
              ->afterStateUpdated(fn (Set $set, ?string $state) => $set('username', strtoupper($state))),
            Forms\Components\TextInput::make('password')
              ->password()
              ->required()
              ->minLength(7),
          ]),
        Section::make('Pilih Karyawan')
          ->description('Form ini digunakan untuk menentukan karyawan yang akan menggunakan akun.')
          ->schema([
            Forms\Components\Select::make('data_karyawan_id')
              ->relationship('karyawan', 'nama')
              ->searchable()
              ->preload(),
            Forms\Components\Select::make('role')
              ->options([
                'Admin' => 'Admin',
                'Leader' => 'Leader',
                'SE/SM' => 'SE/SM',
                'SPG' => 'SPG',
              ])
              ->required()
              ->searchable(),
          ]),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->modifyQueryUsing(function (Builder $query) {
        if (auth()->user()->role === 'Leader') {
          $word = auth()->user()->username;
          $pieces = explode(' ', $word, 3);
          $lastWord = $pieces[0] . ' ' . $pieces[1];
          $query->where('username', 'like', '%' . $lastWord . '%')->where('role', '!=', 'Leader');
          // dd($lastWord);
        }
      })
      ->recordUrl(null)
      ->columns([
        Tables\Columns\TextColumn::make('id')
          ->label('No')
          ->rowIndex()
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('karyawan.nama')
          ->searchable()->sortable(),
        Tables\Columns\TextColumn::make('username')
          ->searchable()->sortable(),
        Tables\Columns\TextColumn::make('role')
          ->formatStateUsing(fn (User $record) => strtoupper($record->role))
          ->searchable()->sortable(),
      ])
      ->query(function (User $q) {
        return $q->where('role', '!=', 'Admin');
      })
      ->filters([
        //
      ])
      ->actions([
        ActionGroup::make([
          Tables\Actions\EditAction::make(),
          Tables\Actions\DeleteAction::make(),
        ]),
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
      'index' => Pages\ListUsers::route('/'),
      'create' => Pages\CreateUser::route('/create'),
      'edit' => Pages\EditUser::route('/{record}/edit'),
    ];
  }
}
