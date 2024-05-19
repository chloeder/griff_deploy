<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class DetailCoverageSPG extends Page
{
  protected static ?string $navigationIcon = 'heroicon-o-document-text';
  protected static bool $shouldRegisterNavigation = false;
  protected static string $view = 'filament.pages.detail-coverage-s-p-g';

  public $salesId;
  public function mount($id)
  {
    $this->salesId = $id;
  }
}
