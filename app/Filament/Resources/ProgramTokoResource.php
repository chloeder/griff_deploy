<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramTokoResource\Pages;
use App\Filament\Resources\ProgramTokoResource\RelationManagers;
use App\Models\ProgramToko;
use App\Models\Toko;
use Carbon\Carbon;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class ProgramTokoResource extends Resource
{
  protected static ?string $model = ProgramToko::class;
  protected static ?string $navigationGroup = 'Aksi';
  protected static ?int $navigationSort = 11;
  protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('Program Toko')
          ->description('Form ini digunakan untuk mengatur program toko.')
          ->schema([
            Forms\Components\Select::make('toko_id')
              ->options(function () {
                if (Auth::user()->role === 'Leader') {
                  return Toko::query()
                    ->join('leaders', 'leaders.id', '=', 'tokos.leader_id')
                    ->where('leaders.user_id', Auth::user()->id)
                    ->pluck('tokos.nama_toko', 'tokos.id');
                  // dd($data);
                } else {
                  return Toko::all()->pluck('nama_toko', 'id');
                }
              })
              ->label('Toko')
              ->required()
              ->searchable()
              ->preload(),
            Forms\Components\Select::make('sewa_display')
              ->required()
              ->options([
                'END GONDOLA' => 'END GONDOLA',
                'TOP GONDOLA' => 'TOP GONDOLA',
                'BLOCK SHELVING' => 'BLOCK SHELVING',
                'COC' => 'COC',
                'FLOOR' => 'FLOOR',
                'DANCING UP' => 'DANCING UP',
                'SHELVING' => 'SHELVING',
                'TIDAK SEWA DISPLAY' => 'TIDAK SEWA DISPLAY',
              ])
              ->searchable(),
            Forms\Components\TextInput::make('sewa_target')
              ->mask(RawJs::make('$money($input)'))
              ->stripCharacters(',')
              ->required()
              ->numeric()
              ->maxLength(255),
            Forms\Components\TextInput::make('cashback')
              ->required()
              ->numeric()
              ->maxLength(255),
            Forms\Components\TextInput::make('cashback_target')
              ->mask(RawJs::make('$money($input)'))
              ->stripCharacters(',')
              ->required()
              ->numeric()
              ->maxLength(255),
            // Flatpickr::make('tanggal_pembuatan')
            //   ->theme(\Coolsam\FilamentFlatpickr\Enums\FlatpickrTheme::DARK)
            //   ->monthSelect(),
            DateRangePicker::make('tanggal_pembuatan')
              ->label('Periode Program')
              ->disableCustomRange()
              ->maxDate(Carbon::now()->addMonth())
          ])
          ->columns(3),
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
          $data =  $query->select('program_tokos.*', 'leaders.nama as leader')->join('tokos', 'tokos.id', '=', 'program_tokos.toko_id')->join('leaders', 'leaders.id', '=', 'tokos.leader_id')->where('leaders.nama', 'like', '%' . $lastWord . '%')->get();
          // dd($lastWord);
        } elseif (auth()->user()->role === 'SE/SM') {
          $data = $query->select('program_tokos.*', 'tokos.sales_marketing_id as SE')->join('tokos', 'tokos.id', '=', 'program_tokos.toko_id')->where('sales_marketing_id', auth()->user()->id)->get();
          // dd($data);
        } elseif (auth()->user()->role === 'SPG') {
          $data = $query->select('program_tokos.*', 'tokos.sales_promotion_id as SPG')->join('tokos', 'tokos.id', '=', 'program_tokos.toko_id')->where('sales_promotion_id', auth()->user()->id)->get();
          // dd($data);
        }
      })
      ->recordUrl(null)
      ->columns([
        Tables\Columns\TextColumn::make('id')
          ->label('No')
          ->rowIndex()
          ->sortable(),
        Tables\Columns\TextColumn::make('toko.nama_toko')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('toko.leader.nama')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('toko.klaster.nama')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('toko.sub_klaster.nama')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('toko.sales_marketing.user.username')
          ->label('SE/SM')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('toko.sales_promotion.user.username')
          ->label('SPG')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('sewa_display')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('sewa_target')
          ->prefix('Rp. ')
          ->numeric(locale: 'id')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('cashback')
          ->suffix('%')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('cashback_target')
          ->prefix('Rp. ')
          ->numeric(locale: 'id')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('tanggal_pembuatan')
          ->label('Periode Program')
          ->date()
          ->sortable()
          ->formatStateUsing(function (string $state): string {
            return Carbon::parse($state)->format('F Y');
          }),


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
        Tables\Actions\BulkActionGroup::make([]),
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
      'index' => Pages\ListProgramTokos::route('/'),
      'create' => Pages\CreateProgramToko::route('/create'),
      'edit' => Pages\EditProgramToko::route('/{record}/edit'),
    ];
  }
}
