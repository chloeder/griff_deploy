<?php

use App\Filament\Pages\DetailAbsen;
use App\Filament\Pages\ListTransaksiNoPo;
use App\Filament\Pages\ListTransaksiProduk;
use App\Filament\Pages\ListTransaksiStock;
use App\Livewire\TransaksiProduk;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
  return redirect('/admin/login');
});

Route::get('transaksi-omset/{id}', ListTransaksiProduk::class)->name('transaksi-produk')->middleware('auth');
Route::get('transaksi-no-po/{id}', ListTransaksiNoPo::class)->name('transaksi-no-po')->middleware('auth');
Route::get('transaksi-stock/{id}', ListTransaksiStock::class)->name('transaksi-stock')->middleware('auth');
Route::get('detail-absen/{id}', DetailAbsen::class)->name('detail-absen')->middleware('auth');
