<?php

namespace App\Filament\Pages;

use App\Models\PerencanaanPerjalananPermanent;
use App\Models\PerencanaanPerjalananPermanentStock;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class LaporanCoverageSPG extends Page implements HasTable
{
  use InteractsWithTable;
  protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
  protected static ?string $navigationLabel = 'Coverage SPG';
  protected static ?int $navigationSort = 17;
  protected static ?string $navigationGroup = 'Laporan';
  protected static string $view = 'filament.pages.laporan-coverage-s-p-g';
  public static function canAccess(): bool
  {
    return auth()->user()->role === 'Admin' || auth()->user()->role === 'Leader' || auth()->user()->role === 'SPG';
  }

  public function table(Table $table): Table
  {
    config()->set('database.connections.mysql.strict', false);
    DB::reconnect();
    return $table
      ->modifyQueryUsing(function (Builder $query) {
        if (auth()->user()->role === 'Leader') {
          $word = auth()->user()->username;
          $pieces = explode(' ', $word, 3);
          $lastWord = $pieces[0] . ' ' . $pieces[1];
          $data = $query->select('perencanaan_perjalanan_permanent_stocks.*', 'leaders.nama as leader')
            ->join('leaders', 'leaders.id', '=', 'perencanaan_perjalanan_permanent_stocks.leader_id')
            ->join('users', 'users.id', '=', 'perencanaan_perjalanan_permanent_stocks.sales_id')
            ->where('role', 'SPG')
            ->where('leaders.nama', 'like', '%' . $lastWord . '%')
            ->groupBy(['sales_id', 'sub_klaster_id']);
        } elseif (auth()->user()->role === 'SPG') {
          $data = $query->where('sales_id', auth()->user()->id)
            ->groupBy('sales_id');
        } else {
          $query->join('users', 'users.id', '=', 'perencanaan_perjalanan_permanent_stocks.sales_id')
            ->where('role', 'SPG')
            ->groupBy(['sales_id', 'sub_klaster_id']);
        }
      })
      ->poll('10s')
      ->query(PerencanaanPerjalananPermanentStock::query())
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
        TextColumn::make('plan')
          ->label('PLAN')
          ->state(function (PerencanaanPerjalananPermanentStock $record): string {
            $dari = session('Dari');
            $sampai = session('Sampai');
            $data =  $record->join('users', 'users.id', '=', 'perencanaan_perjalanan_permanent_stocks.sales_id')
              ->where('role', 'SPG')
              ->whereBetween('tanggal', [$dari, $sampai])
              ->get();
            return $data->where('sales_id', $record->sales_id)
              ->where('sub_klaster_id', $record->sub_klaster_id)
              ->count();
          }),
        TextColumn::make('visit')
          ->label('VISIT')
          ->state(function (PerencanaanPerjalananPermanentStock $record): string {
            $dari = session('Dari');
            $sampai = session('Sampai');
            $data = $record->join('users', 'users.id', '=', 'perencanaan_perjalanan_permanent_stocks.sales_id')
              ->where('role', 'SPG')
              ->where('pjp_status', 'VISIT')
              ->whereBetween('tanggal', [$dari, $sampai])
              ->get();
            return $data->where('sales_id', $record->sales_id)
              ->where('sub_klaster_id', $record->sub_klaster_id)
              ->count();
          }),
        TextColumn::make('ec')
          ->label('EC')
          ->state(function ($record): string {
            $dari = session('Dari');
            $sampai = session('Sampai');
            $data = PerencanaanPerjalananPermanent::join('users', 'users.id', '=', 'perencanaan_perjalanan_permanents.sales_id')
              ->where('role', 'SPG')
              ->where('pjp_status', 'VISIT')
              ->where('omset_po', '>', 0)
              ->where('alasan', null)
              ->whereBetween('tanggal', [$dari, $sampai])
              ->get();
            // dd($data->toArray());
            return $data->where('sales_id', $record->sales_id)
              ->where('sub_klaster_id', $record->sub_klaster_id)
              ->count();
          }),
        TextColumn::make('oa')
          ->label('OA')
          ->state(function ($record): string {
            $dari = session('Dari');
            $sampai = session('Sampai');
            $data = PerencanaanPerjalananPermanent::join('users', 'users.id', '=', 'perencanaan_perjalanan_permanents.sales_id')
              ->where('role', 'SPG')
              ->where('pjp_status', 'VISIT')
              ->where('omset_po', '>', 0)
              ->where('alasan', null)
              ->whereBetween('tanggal', [$dari, $sampai])
              ->groupBy('toko_id')
              ->get();
            $result = $data->where('sales_id', $record->sales_id)
              ->where('sub_klaster_id', $record->sub_klaster_id)
              ->count();
            return $result;
          }),
      ])
      ->filters([
        Filter::make('tanggal')
          ->form([
            DatePicker::make('Dari')
              ->default(now()->startOfMonth()),

            DatePicker::make('Sampai')
              ->default(now()->endOfMonth()),
          ])
          ->query(function (Builder $query, array $data): Builder {
            // Store the selected date range in the session
            session(['Dari' => $data['Dari']]);
            session(['Sampai' => $data['Sampai']]);

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
          ->url(fn (PerencanaanPerjalananPermanentStock $record): string => route('detail-coverage-spg', ['id' => $record->sales_id, 'sub_klaster_id' => $record->sub_klaster_id]))
          ->icon('heroicon-o-eye')
      ])
      ->bulkActions([
        // ExportBulkAction::make(),
      ]);
  }
}
