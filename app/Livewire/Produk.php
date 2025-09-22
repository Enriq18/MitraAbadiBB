<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Produk as ModelProduk;
use Livewire\WithFileUploads;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Produk as ImportProduk;

class Produk extends Component
{
    use WithFileUploads;

    public $pilihanMenu = 'lihat';
    public $nama;
    public $kode;
    public $merek;
    public $persamaan;
    public $harga;
    public $produkTerpilih;
    public $fileExcel;
    public $kataKunci = '';
    public $semuaProduk = [];
    public $minimal_stok_toko = 0;
    public $minimal_stok_toko_gudang = 0;

    public function importExcel()
    {
        $this->validate([
            'fileExcel' => 'required|mimes:xlsx,csv'
        ]);

        Excel::import(new ImportProduk, $this->fileExcel->getRealPath());
        $this->reset();
        session()->flash('message', 'Produk berhasil diimport!');
    }

    public function pilihEdit($id)
    {
        $this->produkTerpilih = ModelProduk::findOrFail($id);
        $this->nama = $this->produkTerpilih->nama;
        $this->kode = $this->produkTerpilih->kode;
        $this->merek = $this->produkTerpilih->merek;
        $this->persamaan = $this->produkTerpilih->persamaan;
        $this->harga = $this->produkTerpilih->harga;
        $this->minimal_stok_toko = $this->produkTerpilih->minimal_stok_toko;
        $this->minimal_stok_toko_gudang = $this->produkTerpilih->minimal_stok_toko_gudang;
        $this->pilihanMenu = "edit";
    }

    public function pilihHapus($id)
    {
        $this->produkTerpilih = ModelProduk::findOrFail($id);
        $this->pilihanMenu = "hapus";
    }

    public function hapus()
    {
        if ($this->produkTerpilih->stokGudang()->exists()) {
            session()->flash('error', 'Produk ini tidak dapat dihapus karena masih tersedia di stok gudang.');
            $this->reset(['produkTerpilih']);
            $this->pilihanMenu = 'lihat';
            return;
        }

        $this->produkTerpilih->delete();
        session()->flash('message', 'Produk berhasil dihapus.');
        $this->reset();
        $this->resetFilter();
    }

    public function batal()
    {
        $this->reset();
    }

    public function simpanEdit()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => ['required', 'unique:produks,kode,' . $this->produkTerpilih->id],
            'merek' => 'required',
            'harga' => 'required',
            'minimal_stok_toko' => 'required|numeric|min:0',
            'minimal_stok_toko_gudang' => 'required|numeric|min:0',
        ], [
            'nama.required' => 'Nama harus diisi',
            'kode.required' => 'Kode harus diisi',
            'kode.unique' => 'Kode sudah digunakan',
            'merek.required' => 'Merek harus diisi',
            'harga.required' => 'Harga harus diisi',
            'minimal_stok_toko.required' => 'Minimal stok toko harus diisi',
            'minimal_stok_toko_gudang.required' => 'Minimal stok toko + gudang harus diisi',
        ]);

        $simpan = $this->produkTerpilih;
        $simpan->nama = $this->nama;
        $simpan->kode = $this->kode;
        $simpan->merek = $this->merek;
        $simpan->persamaan = $this->persamaan;
        $simpan->harga = $this->harga;
        $simpan->minimal_stok_toko = $this->minimal_stok_toko;
        $simpan->minimal_stok_toko_gudang = $this->minimal_stok_toko_gudang;
        $simpan->save();

        $this->reset();
        $this->pilihanMenu = 'lihat';
    }

    public function simpan()
    {
        $this->validate([
            'nama' => 'required',
            'kode' => ['required', 'unique:produks,kode'],
            'merek' => 'required',
            'harga' => 'required',
            'minimal_stok_toko' => 'required|numeric|min:0',
            'minimal_stok_toko_gudang' => 'required|numeric|min:0',
        ], [
            'nama.required' => 'Nama harus diisi',
            'kode.required' => 'Kode harus diisi',
            'kode.unique' => 'Kode sudah digunakan',
            'merek.required' => 'Merek harus diisi',
            'harga.required' => 'Harga harus diisi',
            'minimal_stok_toko.required' => 'Minimal stok toko harus diisi',
            'minimal_stok_toko_gudang.required' => 'Minimal stok toko + gudang harus diisi',
        ]);

        $simpan = new ModelProduk();
        $simpan->nama = $this->nama;
        $simpan->kode = $this->kode;
        $simpan->merek = $this->merek;
        $simpan->persamaan = $this->persamaan;
        $simpan->harga = $this->harga;
        $simpan->minimal_stok_toko = $this->minimal_stok_toko;
        $simpan->minimal_stok_toko_gudang = $this->minimal_stok_toko_gudang;
        $simpan->save();

        $this->reset(['nama', 'kode', 'merek', 'persamaan', 'harga', 'minimal_stok_toko', 'minimal_stok_toko_gudang']);
        $this->pilihanMenu = 'lihat';
    }

    public function pilihMenu($menu)
    {
        $this->pilihanMenu = $menu;
    }

    public function render()
    {
        $this->filterBerdasarkanKriteria();
        return view('livewire.produk');
    }

    public function resetFilter()
    {
        $this->kataKunci = '';
    }

    public function filterBerdasarkanKriteria()
    {
        $query = ModelProduk::query();

        if (!empty($this->kataKunci)) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->kataKunci . '%')
                    ->orWhere('kode', 'like', '%' . $this->kataKunci . '%')
                    ->orWhere('merek', 'like', '%' . $this->kataKunci . '%')
                    ->orWhere('persamaan', 'like', '%' . $this->kataKunci . '%');
            });
        }

        $this->semuaProduk = $query->get();
    }
}
