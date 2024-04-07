<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Toko;
use Filament\Tables;
use App\Models\Sales;
use App\Models\Klaster;
use Filament\Forms\Form;
use App\Models\SubKlaster;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use App\Models\PerencanaanPerjalananPermanentStock;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PerencanaanPerjalananPermanentStockResource\Pages;
use App\Filament\Resources\PerencanaanPerjalananPermanentStockResource\RelationManagers;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Collection;

class PerencanaanPerjalananPermanentStockResource extends Resource
{
  protected static ?string $model = PerencanaanPerjalananPermanentStock::class;

  protected static ?string $navigationLabel = 'PJP Stock';
  protected static ?string $title = 'PJP Stock';
  protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
  protected static ?string $navigationGroup = 'Aksi';
  protected static ?int $navigationSort = 10;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
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
                $set('sales_id', null);
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
                $set('sales_id', null);
                $set('toko_id', null);
              })
              ->searchable()
              ->required(),
            Forms\Components\Select::make('sub_klaster_id')
              ->label('Sub Klaster')
              ->options(fn (Get $get): Collection => SubKlaster::query()
                ->where('klaster_id', $get('klaster_id'))
                ->pluck('nama', 'id'))
              ->afterStateUpdated(function (Set $set) {
                $set('sales_id', null);
                $set('toko_id', null);
              })
              ->live()
              ->searchable()
              ->required(),
          ])
          ->columns(3)
          ->disabled(function (string $context): bool {
            if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Leader') {
              return false;
            }
            return $context === 'edit';
          }),
        Section::make('Pilih Toko & Sales')
          ->description('Form ini untuk menentukan toko & sales')
          ->schema([
            Forms\Components\Select::make('sales_id')
              ->label('Sales')
              ->options(fn (Get $get): Collection => Sales::query()
                ->join('users', 'users.id', '=', 'sales.user_id')
                ->where('users.role', 'SPG')
                ->where('klaster_id', $get('klaster_id'))
                ->pluck('username', 'user_id'))
              ->afterStateUpdated(function (Set $set) {
                $set('toko_id', null);
              })
              ->searchable()
              ->live()
              ->required(),
            Forms\Components\Select::make('toko_id')
              ->label('Toko')
              ->options(fn (Get $get): Collection => Toko::query()
                ->where('klaster_id', $get('klaster_id'))
                ->where('sales_marketing_id', $get('sales_id'))
                ->Orwhere('sales_promotion_id', $get('sales_id'))
                ->pluck('nama', 'id'))
              ->searchable()
              ->required(),
          ])
          ->columns(2)->columnSpan(2)
          ->disabled(function (string $context): bool {
            if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Leader') {
              return false;
            }
            return $context === 'edit';
          }),
        Section::make('Pilih Tanggal')
          ->description('Form ini untuk menentukan tanggal')
          ->schema([
            Forms\Components\DatePicker::make('tanggal')
              ->required()
              ->native(false),
          ])
          ->columnSpan(1)
          ->disabled(function (string $context): bool {
            if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Leader') {
              return false;
            }
            return $context === 'edit';
          }),

      ])->columns(3);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->modifyQueryUsing(function (Builder $query) {
        if (auth()->user()->role === 'SPG') {
          $query->where('sales_id', auth()->user()->id);
        }
      })
      ->poll('10s')
      ->columns([
        Tables\Columns\TextColumn::make('id')
          ->label('No')
          ->rowIndex()
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('leader.nama')
          ->toggleable(isToggledHiddenByDefault: true)
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('klaster.nama')
          ->toggleable(isToggledHiddenByDefault: true)
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('sub_klaster.nama')
          ->toggleable(isToggledHiddenByDefault: true)
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('sales.user.username')
          ->toggleable(isToggledHiddenByDefault: false)
          ->label('Sales')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('toko.nama')
          ->toggleable(isToggledHiddenByDefault: false)
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('toko.tipe_toko')
          ->toggleable(isToggledHiddenByDefault: false)
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('tanggal')
          ->toggleable(isToggledHiddenByDefault: false)
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('stock_sum_nilai_sdm')
          ->url(fn (PerencanaanPerjalananPermanentStock $record): string => route('transaksi-stock', ['id' => $record->id]))
          ->badge()
          ->color('success')
          ->label('SDM')
          ->sum('stock', 'nilai_sdm')
          ->prefix('Rp. ')
          ->numeric(locale: 'id')->sortable(),
        Tables\Columns\TextColumn::make('stock_sum_nilai_sdt')
          ->url(fn (PerencanaanPerjalananPermanentStock $record): string => route('transaksi-stock', ['id' => $record->id]))
          ->badge()
          ->color('success')
          ->label('SDT')
          ->sum('stock', 'nilai_sdt')
          ->prefix('Rp. ')
          ->numeric(locale: 'id')->sortable(),
        Tables\Columns\TextColumn::make('stock_sum_nilai_sdp')
          ->url(fn (PerencanaanPerjalananPermanentStock $record): string => route('transaksi-stock', ['id' => $record->id]))
          ->badge()
          ->color('success')
          ->label('SDP')
          ->sum('stock', 'nilai_sdp')
          ->prefix('Rp. ')
          ->numeric(locale: 'id')->sortable(),
        Tables\Columns\TextColumn::make('stock_sum_nilai_sell_stock')
          ->url(fn (PerencanaanPerjalananPermanentStock $record): string => route('transaksi-stock', ['id' => $record->id]))
          ->badge()
          ->color('success')
          ->label('SELL STOCK')
          ->sum('stock', 'nilai_sell_stock')
          ->prefix('Rp. ')
          ->numeric(locale: 'id')->sortable(),
        Tables\Columns\TextColumn::make('pjp_status')
          ->badge()
          ->color(fn (string $state): string => match ($state) {
            'PLAN' => 'info',
            'VISIT' => 'success',
          })
          ->state(function (PerencanaanPerjalananPermanentStock $record): string {
            if ($record->status === 'Disetujui') {
              $record->update(['pjp_status' => 'VISIT']);
              return $record->pjp_status;
            } else {
              $record->update(['pjp_status' => 'PLAN']);
              return $record->pjp_status;
            }
          })
          ->label('PJP Status')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('status')
          ->badge()
          ->color(fn (string $state): string => match ($state) {
            'Pending' => 'warning',
            'Disetujui' => 'success',
            'Ditolak' => 'danger',
          })
          ->label('Status')
          ->searchable()
          ->sortable(),

      ])
      ->filters([
        Filter::make('tanggal')
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
                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
              )
              ->when(
                $data['Sampai'],
                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
              );
          })
      ])
      ->actions([
        ActionGroup::make([
          Tables\Actions\EditAction::make()->openUrlInNewTab(),
          Tables\Actions\DeleteAction::make(),
          Tables\Actions\Action::make('Edit Status')
            ->hidden(Auth::user()->role !== 'Admin' && Auth::user()->role !== 'Leader')
            ->icon('heroicon-o-pencil')
            ->action(function (PerencanaanPerjalananPermanentStock $record, array $data): void {
              $record->status = $data['status'];
              $record->save();

              if ($record->status === 'Ditolak') {
                $record->delete();
                Notification::make()
                  ->title('PJP Berhasil Ditolak')
                  ->success()
                  ->send();
                return;
              }

              Notification::make()
                ->title('PJP Berhasil Disetujui')
                ->success()
                ->send();
            })
            ->form([
              Select::make('status')
                ->options([
                  'Disetujui' => 'Setujui',
                  'Ditolak' => 'Tolak',
                ])
                ->searchable()

            ]),
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
      'index' => Pages\ListPerencanaanPerjalananPermanentStocks::route('/'),
      'create' => Pages\CreatePerencanaanPerjalananPermanentStock::route('/create'),
      'edit' => Pages\EditPerencanaanPerjalananPermanentStock::route('/{record}/edit'),
    ];
  }
}
