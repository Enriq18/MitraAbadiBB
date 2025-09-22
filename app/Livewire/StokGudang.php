<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\StokGudang as ModelStokGudang;
use App\Models\Produk;
use App\Models\Gudang;
use Illuminate\Support\Collection;

class StokGudang extends Component
{
    public $produk_id, $gudang_id, $stok, $tanggal_kadaluarsa;
    public $stokTerpilih;
    public $semuaStok = [];
    public $pilihanMenu = 'lihat';
    public $kataKunci = '';
    public $produk_nama, $gudang_nama;

    public function updatedProdukNama()
    {
        $produk = Produk::where('nama', $this->produk_nama)->first();
        $this->produk_id = $produk->id ?? null;
    }

    public function updatedGudangNama()
    {
        $gudang = Gudang::where('nama', $this->gudang_nama)->first();
        $this->gudang_id = $gudang->id ?? null;
    }


    public function batal()
    {
        $this->reset();
    }

    public function mount()
    {
        $this->resetFilter();
    }

    public function simpan()
    {
        $this->validate([
            'produk_id' => 'required',
            'gudang_id' => 'required',
            'stok' => 'required|integer|min:1',
            'tanggal_kadaluarsa' => 'nullable|date',
        ], [
            'produk_id.required' => 'Nama produk harus diisi',
            'gudang_id.required' => 'Nama gudang harus diisi',
            'stok.required' => 'Jumlah stok harus diisi',
            'stok.min' => 'Jumlah stok minimal 1',
        ]);

        $query = ModelStokGudang::where('produk_id', $this->produk_id)
            ->where('gudang_id', $this->gudang_id);

        if ($this->tanggal_kadaluarsa) {
            $query->whereDate('tanggal_kadaluarsa', $this->tanggal_kadaluarsa);
        } else {
            $query->whereNull('tanggal_kadaluarsa');
        }

        $stokLama = $query->first();

        if ($stokLama) {
            $stokLama->update([
                'stok' => $stokLama->stok + $this->stok,
            ]);
        } else {
            ModelStokGudang::create([
                'produk_id' => $this->produk_id,
                'gudang_id' => $this->gudang_id,
                'stok' => $this->stok,
                'tanggal_kadaluarsa' => $this->tanggal_kadaluarsa,
            ]);
        }

        $this->resetForm();
        $this->pilihMenu('lihat');
    }

    public function simpanEdit()
    {
        $this->validate([
            'produk_id' => 'required',
            'gudang_id' => 'required',
            'stok' => 'required|integer|min:0', // boleh 0
            'tanggal_kadaluarsa' => 'nullable|date',
        ], [
            'produk_id.required' => 'Nama produk harus diisi',
            'gudang_id.required' => 'Nama gudang harus diisi',
            'stok.required' => 'Jumlah stok harus diisi',
        ]);

        if ($this->stok == 0) {
            // Kalau stok 0, hapus batch
            $this->stokTerpilih->delete();
        } else {
            $this->stokTerpilih->update([
                'produk_id' => $this->produk_id,
                'gudang_id' => $this->gudang_id,
                'stok' => $this->stok,
                'tanggal_kadaluarsa' => $this->tanggal_kadaluarsa,
            ]);
        }

        $this->resetForm();
        $this->pilihanMenu = 'lihat';
    }

    public function pilihEdit($id)
    {
        $this->stokTerpilih = ModelStokGudang::findOrFail($id);
        $this->produk_id = $this->stokTerpilih->produk_id;
        $this->gudang_id = $this->stokTerpilih->gudang_id;
        $this->stok = $this->stokTerpilih->stok;
        $this->tanggal_kadaluarsa = $this->stokTerpilih->tanggal_kadaluarsa;

        $this->produk_nama = $this->stokTerpilih->produk->nama ?? '';
        $this->gudang_nama = $this->stokTerpilih->gudang->nama ?? '';

        $this->pilihanMenu = 'edit';
    }


    public function pilihHapus($id)
    {
        $this->stokTerpilih = ModelStokGudang::findOrFail($id);
        $this->pilihanMenu = 'hapus';
    }

    public function hapus()
    {
        if ($this->stokTerpilih) {
            $this->stokTerpilih->delete();
        }

        $this->resetForm();
        $this->pilihanMenu = 'lihat';
    }

    public function resetForm()
    {
        $this->reset([
            'produk_id',
            'gudang_id',
            'stok',
            'tanggal_kadaluarsa',
            'stokTerpilih',
            'produk_nama',
            'gudang_nama',
        ]);
    }

    public function pilihMenu($menu)
    {
        $this->resetForm();
        $this->pilihanMenu = $menu;
    }

    public function render()
    {
        $this->filterBerdasarkanKriteria();
        return view('livewire.stok-gudang', [
            'semuaProduk' => Produk::all(),
            'semuaGudang' => Gudang::all(),
        ]);
    }

    public function resetFilter()
    {
        $this->kataKunci = '';
    }

    public function filterBerdasarkanKriteria()
    {
        $query = ModelStokGudang::with(['produk', 'gudang']);

        if (!empty($this->kataKunci)) {
            $query->where(function ($q) {
                $q->whereHas('produk', function ($sub) {
                    $sub->where('nama', 'like', '%' . $this->kataKunci . '%');
                })->orWhereHas('gudang', function ($sub) {
                    $sub->where('nama', 'like', '%' . $this->kataKunci . '%');
                });
            });
        }

        $stokData = $query->get();

        if ($this->pilihanMenu === 'lihat') {
            $this->semuaStok = $stokData->groupBy(function ($item) {
                return $item->produk_id . '-' . $item->gudang_id;
            })->map(function ($group) {
                $total = $group->sum('stok');
                $first = $group->first();
                $first->stok = $total;
                return $first;
            })->values();
        } else {
            $this->semuaStok = $stokData->sortBy('tanggal_kadaluarsa')->values();
        }
    }
}
