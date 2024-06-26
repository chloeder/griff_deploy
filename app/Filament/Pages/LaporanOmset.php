<?php

namespace App\Filament\Pages;

use App\Models\PerencanaanPerjalananPermanent;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class LaporanOmset extends Page implements HasTable
{
  use InteractsWithTable;

  protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
  protected static ?string $navigationLabel = 'Omset All';
  protected static ?string $title = 'Laporan Omset All';
  protected static ?string $navigationGroup = 'Laporan';
  protected static ?int $navigationSort = 12;
  protected static string $view = 'filament.pages.laporan-omset';


  public static function canAccess(): bool
  {
    return auth()->user()->role === 'Admin' || auth()->user()->role === 'Leader' || auth()->user()->role === 'SE/SM' || auth()->user()->role === 'SPG';
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
            Column::make('toko.nama_toko')->heading('Toko'),
            Column::make('toko.tipe_toko')->heading('Tipe Toko'),
            Column::make('omset_po')->heading('Omset PO')->format(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2),
            Column::make('tanggal')->heading('Tanggal'),
            Column::make('pjp_status')->heading('PJP Status'),
          ])
            ->withFilename('Laporan Omset - ' . date('Y-m-d '))
            ->fromTable()
        ])
          ->hidden(auth()->user()->role === 'SPG' || auth()->user()->role === 'SE/SM'),
      ])
      ->modifyQueryUsing(function (Builder $query) {
        if (auth()->user()->role === 'SPG' || auth()->user()->role === 'SE/SM') {
          $query->where('sales_id', auth()->user()->id)->where('omset_po', '>', 0)->where('pjp_status', 'VISIT')->get();
        } elseif (auth()->user()->role === 'Leader') {
          $word = auth()->user()->username;
          $pieces = explode(' ', $word, 3);
          $lastWord = $pieces[0] . ' ' . $pieces[1];
          $query->leftJoin('leaders', 'leaders.id', '=', 'perencanaan_perjalanan_permanents.leader_id')
            ->select('perencanaan_perjalanan_permanents.*', 'leaders.nama as leader')
            ->where('leaders.nama', 'like', '%' . $lastWord . '%')
            ->where('omset_po', '>', 0)
            ->where('pjp_status', 'VISIT')->get();
        } else {
          $query->where('omset_po', '>', 0)->where('pjp_status', 'VISIT');
        }
      })
      ->poll('10s')
      ->query(PerencanaanPerjalananPermanent::query())
      ->groups([
        Group::make('leader.nama')
          ->label('LEADER')
          ->collapsible(),
        Group::make('klaster.nama')
          ->label('KLASTER')
          ->collapsible(),
        Group::make('sub_klaster.nama')
          ->label('SUB KLASTER')
          ->collapsible(),
        Group::make('sales_id')
          ->label('SALES')
          ->collapsible()
          ->getTitleFromRecordUsing(fn (PerencanaanPerjalananPermanent $record): string => $record->sales->user->username),
        Group::make('toko.nama_toko')
          ->label('TOKO')
          ->collapsible(),
        Group::make('tanggal')
          ->label('TANGGAL')
          ->collapsible(),
      ])
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
        TextColumn::make('toko.nama_toko')
          ->searchable()
          ->sortable(),
        TextColumn::make('toko.tipe_toko')
          ->label('Tipe Toko')
          ->searchable()
          ->sortable(),
        TextColumn::make('omset_po')
          ->badge()
          ->color('success')
          ->label('PO')
          ->prefix('Rp. ')
          ->numeric(locale: 'id')
          ->searchable()
          ->sortable()
          ->summarize(Sum::make()
            ->label('Total Nilai')
            ->numeric(locale: 'id')),
        TextColumn::make('tanggal')
          ->searchable()
          ->sortable(),
        TextColumn::make('pjp_status')
          ->badge()
          ->color(fn (string $state): string => match ($state) {
            'PLAN' => 'info',
            'VISIT' => 'success',
          })
          ->label('PJP Status')
          ->searchable()
          ->sortable(),
      ])
      ->filters([
        Filter::make('tanggal')
          ->form([
            DatePicker::make('Dari'),
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
        Action::make('Lihat')
          ->hidden(Auth::user()->role !== 'Admin' && Auth::user()->role !== 'Leader')
          ->url(fn (PerencanaanPerjalananPermanent $record): string => route('transaksi-produk', ['id' => $record->id]))
          ->icon('heroicon-o-eye')
      ])
      ->bulkActions([]);
  }
}
