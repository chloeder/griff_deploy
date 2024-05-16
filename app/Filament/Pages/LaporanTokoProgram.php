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
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
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
  protected static ?string $navigationLabel = 'Omset Program Toko';
  protected static ?string $title = 'Laporan Omset Program Toko';
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
        if (auth()->user()->role !== 'Admin') {
          $word = auth()->user()->username;
          $pieces = explode(' ', $word, 3);
          $lastWord = $pieces[0] . ' ' . $pieces[1];
          $data = $query->select('program_tokos.*', 'leaders.nama as leader')->join('tokos', 'tokos.id', '=', 'program_tokos.toko_id')->join('leaders', 'leaders.id', '=', 'tokos.leader_id')->where('leaders.nama', 'like', '%' . $lastWord . '%')->get();
        }
      })
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
        TextColumn::make('omset_faktur')
          ->badge()
          ->color('success')
          ->prefix('Rp. ')
          ->numeric(locale: 'id')
        // TextInputColumn::make('omset_faktur')
        //   ->disabled(
        //     function (ProgramToko $record) {
        //       if (auth()->user()->role === 'SE/SM' || auth()->user()->role === 'SPG') {
        //         return true;
        //       } elseif ($record->is_disabled) {
        //         return true;
        //       } else {
        //         return false;
        //       }
        //     }
        //   )
        //   ->afterStateUpdated(function (ProgramToko $record, $state) {
        //     $record->omset_faktur = $state;
        //     $record->save();
        //   })
        //   ->mask(RawJs::make('$money($input)'))
        //   ->sortable()
      ])
      ->filters([])
      ->actions([
        ActionGroup::make([
          Action::make('Edit Omset Faktur')
            ->icon('heroicon-o-pencil')
            ->hidden(auth()->user()->role !== 'Admin' && auth()->user()->role !== 'Leader')
            ->action(function (ProgramToko $record, array $data): void {
              $record->omset_faktur = $data['omset_faktur'];
              $record->save();
              Notification::make()
                ->title('Omset Faktur Toko ' . $record->toko->nama . ' berhasil disimpan')
                ->success()
                ->send();
            })
            ->form([
              TextInput::make('omset_faktur')
                ->required()
                ->mask(RawJs::make('$money($input)'))

            ]),
          // Action::make('Reset')
          //   ->action(function (ProgramToko $record) {
          //     $record->update(
          //       [
          //         'omset_faktur' => 0,
          //         'is_disabled' => false,
          //       ]
          //     );

          //     Notification::make()
          //       ->title('Omset Faktur Toko ' . $record->toko->nama . ' berhasil direset')
          //       ->success()
          //       ->send();
          //   })
          //   ->hidden(auth()->user()->role !== 'Admin' && auth()->user()->role !== 'Leader')
          //   ->button()
          //   ->color('danger'),
        ])
      ])
      ->bulkActions([
        // ExportBulkAction::make(),
      ]);
  }
}
