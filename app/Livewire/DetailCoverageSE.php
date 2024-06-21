<?php

namespace App\Livewire;

use App\Models\PerencanaanPerjalananPermanent;
use Filament\Resources\Concerns\HasTabs;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Resources\Components\Tab;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;


class DetailCoverageSE extends Component implements HasTable, HasForms
{
  use InteractsWithTable;
  use InteractsWithForms;

  public $salesId;

  public function mount($id)
  {
    $this->salesId = $id;
  }

  public function table(Table $table): Table
  {
    return $table
      ->groups([
        Group::make('status')
          ->label('STATUS'),
        Group::make('pjp_status')
          ->label('PJP STATUS'),
      ])
      ->defaultGroup('pjp_status')
      ->query(PerencanaanPerjalananPermanent::query()->where('sales_id', $this->salesId))
      ->poll('10s')
      ->columns([
        TextColumn::make('id')
          ->label('No')
          ->rowIndex()
          ->sortable(),
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
        TextColumn::make('omset_po')
          ->default('-')
          ->badge()
          ->prefix('Rp. ')
          ->numeric(locale: 'id')
          ->color('success')
          ->label('PO')
          ->sortable(),
        TextColumn::make('no_po.alasan')
          ->badge()
          ->color('danger')
          ->label('No-PO')
          ->default('-')
          ->sortable(),
        TextColumn::make('tanggal')
          ->searchable()
          ->sortable(),
        TextColumn::make('pjp_status')
          ->summarize(Count::make()->label('Total PJP Status'))
          ->badge()
          ->color(fn (string $state): string => match ($state) {
            'PLAN' => 'info',
            'VISIT' => 'success',
          })
          ->label('PJP Status')
          ->searchable()
          ->sortable(),
        TextColumn::make('status')
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
              ->default(now()->startOfMonth()),
            DatePicker::make('Sampai')
              ->default(now()->endOfMonth()),
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
      ->actions([])
      ->bulkActions([]);
  }

  public function render()
  {
    return view('livewire.detail-coverage-s-e');
  }
}
