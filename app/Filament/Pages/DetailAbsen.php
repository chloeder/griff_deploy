<?php

namespace App\Filament\Pages;

use App\Models\Absen;
use Filament\Pages\Page;

class DetailAbsen extends Page
{
  protected static ?string $navigationIcon = 'heroicon-o-document-text';
  protected static bool $shouldRegisterNavigation = false;

  protected static string $view = 'filament.pages.detail-absen';
  public $userId;
  public function mount($id)
  {
    $this->userId = Absen::find($id)->user_id;
  }
}
