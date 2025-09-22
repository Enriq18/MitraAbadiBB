<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaksi;
use Carbon\Carbon;

use App\Models\StokGudang;
use App\Models\Produk;

class Beranda extends Component
{
    public $revenueDifference;
    public $yearlySales;
    public $currentMonthRevenue;
    public $lastMonthRevenue;

    public $stokMinimGudangMA;
    public $stokMinimSemuaGudang;

    public function mount()
    {
        $this->calculateRevenueDifference();
        $this->getDailySalesStatistics();

        // Cek stok toko (gudang_id = 1) < minimal
        $this->stokMinimGudangMA = Produk::all()->map(function ($produk) {
            $stokToko = StokGudang::where('produk_id', $produk->id)
                ->where('gudang_id', 1)
                ->sum('stok');

            return [
                'produk' => $produk,
                'stok' => $stokToko,
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


    private function calculateRevenueDifference()
    {
        $currentYearMonth = Carbon::now()->format('Y-m');
        $lastYearMonth = Carbon::now()->subMonth()->format('Y-m');

        $this->currentMonthRevenue = Transaksi::whereRaw("strftime('%Y-%m', created_at) = ?", [$currentYearMonth])
            ->where('status', 'selesai')
            ->sum('total');

        $this->lastMonthRevenue = Transaksi::whereRaw("strftime('%Y-%m', created_at) = ?", [$lastYearMonth])
            ->where('status', 'selesai')
            ->sum('total');
    }

    private function getDailySalesStatistics()
    {
        $this->yearlySales = Transaksi::selectRaw("strftime('%d', created_at) as day, SUM(total) as total")
            ->whereRaw("strftime('%Y-%m', created_at) = ?", [Carbon::now()->format('Y-m')]) // bulan ini
            ->where('status', 'selesai')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->pluck('total', 'day');
    }


    public function render()
    {
        return view('livewire.beranda')->with([
            'revenueDifference' => $this->revenueDifference,
            'yearlySales' => $this->yearlySales,
            'currentMonthRevenue' => $this->currentMonthRevenue,
            'lastMonthRevenue' => $this->lastMonthRevenue
        ]);
    }
}
