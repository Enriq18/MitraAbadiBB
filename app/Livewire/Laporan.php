<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaksi;

class Laporan extends Component
{
    public $day, $month, $year;

    public function updatedDay()
    {
        $this->dispatch('filterChanged');
    }

    public function updatedMonth()
    {
        $this->dispatch('filterChanged');
    }

    public function updatedYear()
    {
        $this->dispatch('filterChanged');
    }

    public function render()
    {
        $query = Transaksi::with(['detailTransaksis.produk', 'user'])
            ->where('status', 'selesai')
            ->orderBy('created_at', 'desc');

        // Terapkan filter jika ada
        if ($this->day) {
            $query->whereDay('created_at', $this->day);
        }

        if ($this->month) {
            $query->whereMonth('created_at', $this->month);
        }

        if ($this->year) {
            $query->whereYear('created_at', $this->year);
        }

        $semuaTransaksi = $query->get();

        return view('livewire.laporan')->with([
            'semuaTransaksi' => $semuaTransaksi
        ]);
    }
}
