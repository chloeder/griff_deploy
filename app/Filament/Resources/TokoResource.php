<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TokoResource\Pages;
use App\Filament\Resources\TokoResource\RelationManagers;
use App\Models\Klaster;
use App\Models\Sales;
use App\Models\SubKlaster;
use App\Models\Toko;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
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
use Illuminate\Support\Facades\Auth;

class TokoResource extends Resource
{
  protected static ?string $model = Toko::class;

  protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
  protected static ?string $navigationGroup = 'Master';
  protected static ?int $navigationSort = 6;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('Tentukan Nama Toko')
          ->description('Form ini akan menentukan nama toko')
          ->schema([
            Forms\Components\TextInput::make('nama')
              ->required()
              ->live(onBlur: true)
              ->afterStateUpdated(fn (Set $set, ?string $state) => $set('nama', strtoupper($state))),
            Forms\Components\Select::make('tipe_toko')
              ->options([
                'MT HPM' => 'MT HPM',
                'MT SPM' => 'MT SPM',
                'MT MNM' => 'MT MNM',
                'SPC' => 'SPC',
                'GT GR' => 'GT GR',
                'GT TT' => 'GT TT',
              ])
              ->searchable()
              ->required(),
          ])->collapsible()->columnSpan(2),

        // Section::make('Pilih Status Toko')
        //   ->schema([
        //     ToggleButtons::make('status')
        //       ->options([
        //         'Aktif' => 'Aktifkan',
        //         'Non-Aktif' => 'Non Aktifkan',
        //       ])
        //       ->colors([
        //         'Aktif' => 'success',
        //         'Non-Aktif' => 'danger',
        //       ])
        //       ->icons([
        //         'Aktif' => 'heroicon-o-check',
        //         'Non-Aktif' => 'heroicon-o-x-mark',
        //       ])->inline()->required()->hidden(fn (Get $get) => $get(auth()->user()->role) === 'SPG'),
        //   ])->collapsible()->columnSpan(1)
        //   ->hidden(Auth::user()->role === 'SE/SM'),

        Section::make('Tentukan Wilayah Toko')
          ->description('Form ini akan menentukan wilayah toko')
          ->schema([
            Forms\Components\Select::make('leader_id')
              ->relationship('leader', 'nama')
              ->searchable()
              ->preload()
              ->live()
              ->afterStateUpdated(function (Set $set) {
                $set('klaster_id', null);
                $set('sub_klaster_id', null);
                $set('sales_marketing_id', null);
                $set('sales_promotion_id', null);
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
                $set('sales_marketing_id', null);
                $set('sales_promotion_id', null);
              })
              ->searchable()
              ->required(),
            Forms\Components\Select::make('sub_klaster_id')
              ->label('Sub Klaster')
              ->options(fn (Get $get): Collection => SubKlaster::query()
                ->where('klaster_id', $get('klaster_id'))
                ->pluck('nama', 'id'))
              ->live()
              ->afterStateUpdated(function (Set $set) {
                $set('sales_marketing_id', null);
                $set('sales_promotion_id', null);
              })
              ->searchable()
              ->required(),
            Forms\Components\Select::make('sales_marketing_id')
              ->label('SE/SM')
              ->options(fn (Get $get): Collection => Sales::query()
                ->join('users', 'users.id', '=', 'sales.user_id')
                ->where('klaster_id', $get('klaster_id'))
                ->where('users.role', 'se/sm')
                ->pluck('username', 'user_id'))
              ->searchable()
              ->required(),
            Forms\Components\Select::make('sales_promotion_id')
              ->label('SPG')
              ->options(fn (Get $get): Collection => Sales::query()
                ->join('users', 'users.id', '=', 'sales.user_id')
                ->where('klaster_id', $get('klaster_id'))
                ->where('users.role', 'spg')
                ->pluck('username', 'user_id'))
              ->searchable(),
          ])->columns(3)->collapsible(),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
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
        Tables\Columns\TextColumn::make('sub_klaster.nama')
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('sales_marketing.user.username')
          ->label('SE/SM')
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('sales_promotion.user.username')
          ->label('SPG')
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('nama')
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('tipe_toko')
          ->sortable()
          ->searchable(),
        // Tables\Columns\TextColumn::make('status')
        //   ->badge()
        //   ->color(fn (string $state): string => match ($state) {
        //     'Aktif' => 'success',
        //     'Non-Aktif' => 'danger',
        //     'Proses' => 'warning',
        //   })
        //   ->sortable()
        //   ->searchable(),
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
      'index' => Pages\ListTokos::route('/'),
      'create' => Pages\CreateToko::route('/create'),
      'edit' => Pages\EditToko::route('/{record:uuid}/edit'),
    ];
  }
}