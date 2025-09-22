<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;

// Livewire Components
use App\Livewire\Beranda;
use App\Livewire\User;
use App\Livewire\Laporan;
use App\Livewire\Produk;
use App\Livewire\Transaksi;
use App\Livewire\Gudang;
use App\Livewire\StokGudang;
use App\Livewire\MutasiBarang;
use App\Livewire\Peran;

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {

    Route::get('/user', User::class)->middleware('cekRole:Admin')->name('user');
    Route::get('/peran', Peran::class)->middleware('cekRole:Admin')->name('peran');

    Route::get('/home', Beranda::class)->middleware('cekRole:Admin,Manager')->name('home');
    Route::get('/cetakLaporan', [HomeController::class, 'cetakLaporan'])->middleware('cekRole:Admin,Manager')->name('cetakLaporan');
    Route::get('/produk', Produk::class)->middleware('cekRole:Admin,Manager')->name('produk');
    Route::get('/gudang', Gudang::class)->middleware('cekRole:Admin,Manager')->name('gudang');

    Route::get('/stok-gudang', StokGudang::class)->middleware('cekRole:Admin,Manager,Kasir')->name('stok-gudang');
    Route::get('/mutasi-barang', MutasiBarang::class)->middleware('cekRole:Admin,Manager,Kasir')->name('mutasi.barang');
    Route::get('/transaksi', Transaksi::class)->middleware('cekRole:Kasir,Manager,Admin')->name('transaksi');
    Route::get('/laporan', Laporan::class)->middleware('cekRole:Admin,Manager,Kasir')->name('laporan');
});
