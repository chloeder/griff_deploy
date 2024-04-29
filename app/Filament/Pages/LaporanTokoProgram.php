<?php

namespace App\Filament\Pages;

use Filament\Forms\Set;
use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\ProgramToko;
use Filament\Support\RawJs;
use Illuminate\Support\Str;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use pxlrbt\FilamentExcel\Columns\Column;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Actions\DeleteBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use App\Models\PerencanaanPerjalananPermanent;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Concerns\InteractsWithTable;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class LaporanTokoProgram extends Page implements HasTable
{
  use InteractsWithTable;
  protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
  protected static ?string $navigationLabel = 'Omset Program';
  protected static ?string $title = 'Laporan Omset Program';
  protected static ?int $navigationSort = 13;
  protected static ?string $navigationGroup = 'Laporan';
  protected static string $view = 'filament.pages.laporan-toko-program';
  public static function canAccess(): bool
  {
    return auth()->user()->role === 'Admin' || auth()->user()->role === 'Leader' || auth()->user()->role === 'SE/SM' || auth()->user()->role === 'SPG';
  }
  public function table(Table $table): Table
  {
    return $table
      ->modifyQueryUsing(function (Builder $query) {
        if (auth()->user()->role === 'Leader') {
          $word = auth()->user()->username;
          $pieces = explode(' ', $word, 2);
          $lastWord = end($pieces);
          $query->leftJoin('tokos', 'tokos.id', '=', 'program_tokos.toko_id')->leftJoin('leaders', 'leaders.id', '=', 'tokos.leader_id')->where('leaders.nama', 'like', '%' . $lastWord . '%')->get();
          // dd($data);
        }
      })
      ->poll('0s')
      ->query(ProgramToko::query())
      ->columns([
        TextColumn::make('toko.leader.nama')
          ->toggleable(isToggledHiddenByDefault: true)
          ->searchable()
          ->sortable(),
        TextColumn::make('toko.klaster.nama')
          ->toggleable(isToggledHiddenByDefault: true)
          ->searchable()
          ->sortable(),
        TextColumn::make('toko.sub_klaster.nama')
          ->toggleable(isToggledHiddenByDefault: true)
          ->searchable()
          ->sortable(),
        TextColumn::make('toko.sales_marketing.user.username')
          ->toggleable(isToggledHiddenByDefault: true)
          ->label('SE/SM')
          ->searchable()
          ->sortable(),
        TextColumn::make('toko.sales_promotion.user.username')
          ->toggleable(isToggledHiddenByDefault: true)
          ->label('SPG')
          ->searchable()
          ->sortable(),
        TextColumn::make('toko.nama')
          ->searchable()
          ->sortable(),
        TextColumn::make('toko.tipe_toko')
          ->label('Tipe Toko')
          ->searchable()
          ->sortable(),
        TextColumn::make('sewa_display')
          ->label('Sewa Display')
          ->searchable()
          ->sortable(),
        TextColumn::make('sewa_target')
          ->label('Sewa Target')
          ->prefix('Rp. ')
          ->numeric(locale: 'id')
          ->searchable()
          ->sortable(),
        TextColumn::make('cashback')
          ->label('Cashback')
          ->suffix('%')
          ->searchable()
          ->sortable(),
        TextColumn::make('cashback_target')
          ->label('Cashback Target')
          ->prefix('Rp. ')
          ->numeric(locale: 'id')
          ->searchable()
          ->sortable(),
        TextColumn::make('omset_po')
          ->badge()
          ->color('success')
          ->state(function ($record): string {
            return $record->join('perencanaan_perjalanan_permanents', 'perencanaan_perjalanan_permanents.toko_id', '=', 'program_tokos.toko_id')->where('program_tokos.toko_id', $record->toko_id)->sum('omset_po');
          })
          ->label('Omset Sistem')
          ->prefix('Rp. ')
          ->numeric(locale: 'id')
          ->searchable()
          ->sortable(),
        TextInputColumn::make('omset_faktur')
          ->disabled(
            fn (ProgramToko $record): bool => $record->omset_faktur != 0
          )
          ->afterStateUpdated(function (ProgramToko $record, $state) {
            $record->update([
              'omset_faktur' => $state,
            ]);
          })
          ->mask(RawJs::make('$money($input)'))
          ->sortable()
      ])
      ->filters([
        // Filter::make('created_at')
        //   ->form([
        //     DatePicker::make('Dari'),
        //     DatePicker::make('Sampai')
        //       ->default(now()),
        //   ])
        //   ->query(function (Builder $query, array $data): Builder {
        //     return $query
        //       ->when(
        //         $data['Dari'],
        //         fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
        //       )
        //       ->when(
        //         $data['Sampai'],
        //         fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
        //       );
        //   })
      ])
      ->actions([
        Action::make('Simpan')
          ->action(function (ProgramToko $record) {
            Notification::make()
              ->title('Omset Faktur Toko ' . $record->toko->nama . ' berhasil disimpan')
              ->success()
              ->send();
          })
          ->button(),
        Action::make('Reset')
          ->action(function (ProgramToko $record) {
            $record->update([
              'omset_faktur' => 0,
            ]);

            Notification::make()
              ->title('Omset Faktur Toko ' . $record->toko->nama . ' berhasil direset')
              ->success()
              ->send();
          })
          ->button()
          ->color('danger'),
      ])
      ->bulkActions([
        // ExportBulkAction::make(),
      ]);
  }
}
