<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiProdukResource\Pages\ListTransaksiProduks;
use Filament\Forms;
use Filament\Tables;
use App\Models\Klaster;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\SubKlaster;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PerencanaanPerjalananPermanent;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PerencanaanPerjalananPermanentResource\Pages;
use App\Filament\Resources\TransaksiProdukResource\Pages\CreateTransaksiProduk;
use App\Filament\Resources\TransaksiProdukResource\Pages\EditTransaksiProduk;
use App\Livewire\FormTransaksiProdukWidget;
use App\Models\Sales;
use App\Models\Toko;
use App\Models\TransaksiProduk;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Route;

class PerencanaanPerjalananPermanentResource extends Resource
{

  protected static ?string $model = PerencanaanPerjalananPermanent::class;
  protected static ?string $navigationLabel = 'PJP Omset';
  protected static ?string $pluralModelLabel = 'PJP Omset';
  protected static ?string $title = 'PJP Omset';
  protected static ?string $navigationIcon = 'heroicon-o-truck';
  protected static ?string $navigationGroup = 'Aksi';
  protected static ?int $navigationSort = 9;
  public static function getRecordTitle(?Model $record): string|null|Htmlable
  {
    return $record->name;
  }
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
                $set('toko_id', null);
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
                ->where('users.role', 'SE/SM')
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
      ->recordUrl(null)
      ->modifyQueryUsing(function (Builder $query) {
        if (auth()->user()->role === 'SE/SM' || auth()->user()->role === 'SPG') {
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
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('klaster.nama')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('sub_klaster.nama')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('sales.user.username')
          ->label('Sales')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('toko.nama')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('omset_po')
          ->url(fn (PerencanaanPerjalananPermanent $record): string => route('transaksi-produk', ['id' => $record->id]))
          ->badge()
          ->prefix('Rp. ')
          ->numeric(locale: 'id')
          ->color('success')
          ->label('PO')
          ->sortable(),
        Tables\Columns\TextColumn::make('no_po.alasan')
          ->url(fn (PerencanaanPerjalananPermanent $record): string => route('transaksi-no-po', ['id' => $record->id]))
          ->badge()
          ->color('danger')
          ->label('No-PO')
          ->default('-')
          ->sortable(),
        Tables\Columns\TextColumn::make('tanggal')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('pjp_status')
          ->badge()
          ->color(fn (string $state): string => match ($state) {
            'PLAN' => 'info',
            'VISIT' => 'success',
          })
          ->state(function (PerencanaanPerjalananPermanent $record): string {
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
          Tables\Actions\EditAction::make(),
          Tables\Actions\DeleteAction::make(),
          Tables\Actions\Action::make('Edit Status')
            ->hidden(Auth::user()->role !== 'Admin' && Auth::user()->role !== 'Leader')
            ->icon('heroicon-o-pencil')
            ->action(function (PerencanaanPerjalananPermanent $record, array $data): void {
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
    return [];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListPerencanaanPerjalananPermanents::route('/'),
      'create' => Pages\CreatePerencanaanPerjalananPermanent::route('/create'),
      'edit' => Pages\EditPerencanaanPerjalananPermanent::route('/{record}/edit'),

      // 'transaksis.index' => ListTransaksiProduks::route('/{parent}/transaksi'),
      // 'transaksis.create' => CreateTransaksiProduk::route('/{parent}/transaksi/create'),
      // 'transaksis.edit' => EditTransaksiProduk::route('/{parent}/transaksi/{record}/edit'),
    ];
  }
}
