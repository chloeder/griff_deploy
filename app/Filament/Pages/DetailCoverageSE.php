<?php

namespace App\Filament\Pages;

use App\Models\PerencanaanPerjalananPermanent;
use Filament\Pages\Page;
use Filament\Resources\Components\Tab;
use Filament\Resources\Concerns\HasTabs;
use Illuminate\Database\Eloquent\Builder;

class DetailCoverageSE extends Page
{
  protected static ?string $navigationIcon = 'heroicon-o-document-text';
  protected static bool $shouldRegisterNavigation = false;
  protected static string $view = 'filament.pages.detail-coverage-s-e';

  public $salesId;
  public function mount($id)
  {
    $this->salesId = $id;
  }
}
