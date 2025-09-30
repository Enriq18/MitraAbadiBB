<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaksi;
use Carbon\Carbon;
use App\Models\StokGudang;
use App\Models\Produk;
use App\Models\Gudang;

class Beranda extends Component
{
    public $revenueDifference;
    public $yearlySales;
    public $currentMonthRevenue;
    public $lastMonthRevenue;

    public $stokMinimGudangTerpilih;
    public $stokMinimSemuaGudang;

    public $gudangTerpilihId = 1; // Default gudang 1
    public $gudangList;

    public function mount()
    {
        $this->gudangList = Gudang::all();
        $this->cekStokMinimum();
    }

    public function updatedGudangTerpilihId()
    {
        // Ketika gudang berubah, cek ulang stok
        $this->cekStokMinimum();
    }

    private function cekStokMinimum()
    {
        // Cek stok gudang terpilih < minimal
        $this->stokMinimGudangTerpilih = Produk::all()->map(function ($produk) {
            $stokGudang = StokGudang::where('produk_id', $produk->id)
                ->where('gudang_id', $this->gudangTerpilihId)
                ->sum('stok');

            return [
                'produk' => $produk,
                'stok' => $stokGudang,
            ];
        })->filter(function ($item) {
            return $item['stok'] < $item['produk']->minimal_stok_toko;
        })->values();

        // Cek stok semua gudang < minimal
        $this->stokMinimSemuaGudang = Produk::all()->filter(function ($produk) {
            $stokTotal = StokGudang::where('produk_id', $produk->id)->sum('stok');
            return $stokTotal < $produk->minimal_stok_toko_gudang;
        })->map(function ($produk) {
            return [
                'produk' => $produk,
                'total_stok' => StokGudang::where('produk_id', $produk->id)->sum('stok'),
            ];
        })->values();
    }
}
