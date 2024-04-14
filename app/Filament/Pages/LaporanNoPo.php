<?php

namespace App\Filament\Pages;

use App\Models\PerencanaanPerjalananPermanent;
use App\Models\TransaksiNoPo;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class LaporanNoPo extends Page implements HasTable
{
  use InteractsWithTable;
  protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
  protected static ?string $navigationGroup = 'Laporan';
  protected static ?string $navigationLabel = 'No PO';
  protected static ?int $navigationSort = 14;
  protected static string $view = 'filament.pages.laporan-no-po';
  public static function canAccess(): bool
  {
    return auth()->user()->role === 'Admin' || auth()->user()->role === 'Leader';
  }
  public function table(Table $table): Table
  {
    return $table
      ->headerActions([
        ExportAction::make()->exports([
          ExcelExport::make()->withColumns([
            Column::make('leader.nama')->heading('Leader'),
            Column::make('klaster.nama')->heading('Klaster'),
            Column::make('sub_klaster.nama')->heading('Sub Klaster'),
            Column::make('sales.user.username')->heading('Sales'),
            Column::make('toko.nama')->heading('Toko'),
            Column::make('toko.tipe_toko')->heading('Tipe Toko'),
            Column::make('no_po.alasan')->heading('Alasan No PO'),
            Column::make('tanggal')->heading('Tanggal'),
            Column::make('pjp_status')->heading('PJP Status'),
          ])
            ->withFilename('Laporan Omset - ' . date('Y-m-d '))
            ->fromTable()
        ]),
      ])
      ->modifyQueryUsing(function (Builder $query) {
        $query->where('pjp_status', 'VISIT')->where('alasan', '!=', null);
      })
      ->poll('10s')
      ->query(PerencanaanPerjalananPermanent::query())
      ->columns([
        TextColumn::make('leader.nama')
          ->searchable()
          ->sortable(),
        TextColumn::make('klaster.nama')
          ->searchable()
          ->sortable(),
        TextColumn::make('sub_klaster.nama')
          ->searchable()
          ->sortable(),
        TextColumn::make('sales.user.username')
          ->label('Sales')
          ->searchable()
          ->sortable(),
        TextColumn::make('toko.nama')
          ->searchable()
          ->sortable(),
        TextColumn::make('toko.tipe_toko')
          ->label('Tipe Toko')
          ->searchable()
          ->sortable(),
        TextColumn::make('pjp_status')
          ->badge()
          ->color(fn (string $state): string => match ($state) {
            'PLAN' => 'info',
            'VISIT' => 'info',
          })
          ->label('PJP Status')
          ->searchable()
          ->sortable(),
        TextColumn::make('no_po.alasan')
          ->badge()
          ->label('No PO')
          ->searchable()
          ->sortable(),
      ])
      ->filters([
        Filter::make('created_at')
          ->form([
            DatePicker::make('Dari'),
            DatePicker::make('Sampai')
              ->default(now()),
          ])
          ->query(function (Builder $query, array $data): Builder {
            return $query
              ->when(
                $data['Dari'],
                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
              )
              ->when(
                $data['Sampai'],
                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
              );
          })
      ])
      ->actions([
        Action::make('Lihat')
          ->url(fn (PerencanaanPerjalananPermanent $record): string => route('transaksi-no-po', ['id' => $record->id]))
          ->icon('heroicon-o-eye')
      ])
      ->bulkActions([
        ExportBulkAction::make(),
      ]);
  }
}
