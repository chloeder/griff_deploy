<?php

namespace App\Providers;

use App\Models\DataKaryawan;
use App\Models\Klaster;
use App\Models\Leader;
use App\Models\PerencanaanPerjalananPermanent;
use App\Models\Sales;
use App\Models\StockKeepingUnit;
use App\Models\SubKlaster;
use App\Models\Toko;
use App\Policies\DataKaryawanPolicy;
use App\Policies\KlasterPolicy;
use App\Policies\LeaderPolicy;
use App\Policies\PerencanaanPerjalananPermanentPolicy;
use App\Policies\SalesPolicy;
use App\Policies\SKUPolicy;
use App\Policies\StockKeepingUnitPolicy;
use App\Policies\SubKlasterPolicy;
use App\Policies\TokoPolicy;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

  protected $policies = [
    PerencanaanPerjalananPermanent::class => PerencanaanPerjalananPermanentPolicy::class,
    Leader::class => LeaderPolicy::class,
    Klaster::class => KlasterPolicy::class,
    SubKlaster::class => SubKlasterPolicy::class,
    Sales::class => SalesPolicy::class,
    StockKeepingUnit::class => StockKeepingUnitPolicy::class,
    Toko::class => TokoPolicy::class,
    DataKaryawan::class => DataKaryawanPolicy::class,
  ];
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
  }
}
