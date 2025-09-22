<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Gudang as ModelGudang;

class Gudang extends Component
{
    public $pilihanMenu = 'lihat';
    public $nama;
    public $lokasi;
    public $deskripsi;
    public $pengurus;
    public $gudangTerpilih;
    public $kataKunci = '';
    public $semuaGudang = [];

    public function mount()
    {
        $this->resetFilter();
    }

    public function simpan()
    {
        $this->validate([
            'nama' => 'required',
            'lokasi' => 'required',
            'deskripsi' => 'required',
            'pengurus' => 'required'
        ], [
            'nama.required' => 'Nama harus diisi',
            'lokasi.required' => 'Lokasi harus diisi',
            'deskripsi.required' => 'Deskripsi harus diisi',
            'pengurus.required' => 'Pengurus harus diisi',
        ]);

        $simpan = new ModelGudang();
        $simpan->nama = $this->nama;
        $simpan->lokasi = $this->lokasi;
        $simpan->deskripsi = $this->deskripsi;
        $simpan->pengurus = $this->pengurus;
        $simpan->save();

        $this->reset(['nama', 'lokasi', 'deskripsi', 'pengurus']);
        $this->pilihanMenu = 'lihat';
        $this->resetFilter();
    }

    public function pilihEdit($id)
    {
        $this->gudangTerpilih = ModelGudang::findOrFail($id);
        $this->nama = $this->gudangTerpilih->nama;
        $this->lokasi = $this->gudangTerpilih->lokasi;
        $this->deskripsi = $this->gudangTerpilih->deskripsi;
        $this->pengurus = $this->gudangTerpilih->pengurus;
        $this->pilihanMenu = 'edit';
    }

    public function simpanEdit()
    {
        $this->validate([
            'nama' => 'required',
            'lokasi' => 'required',
            'deskripsi' => 'required',
            'pengurus' => 'required'
        ], [
            'nama.required' => 'Nama harus diisi',
            'lokasi.required' => 'Lokasi harus diisi',
            'deskripsi.required' => 'Deskripsi harus diisi',
            'pengurus.required' => 'Pengurus harus diisi',
        ]);

        $edit = $this->gudangTerpilih;
        $edit->nama = $this->nama;
        $edit->lokasi = $this->lokasi;
        $edit->deskripsi = $this->deskripsi;
        $edit->pengurus = $this->pengurus;
        $edit->save();

        $this->reset(['nama', 'lokasi', 'gudangTerpilih', 'deskripsi', 'pengurus']);
        $this->resetFilter();
        $this->pilihanMenu = 'lihat';
    }

    public function pilihHapus($id)
    {
        $this->gudangTerpilih = ModelGudang::findOrFail($id);
        $this->pilihanMenu = 'hapus';
    }

    public function hapus()
    {
        if ($this->gudangTerpilih->stok()->exists()) {
            session()->flash('error', 'Gudang ini tidak dapat dihapus karena masih memiliki stok.');
            $this->reset(['gudangTerpilih']);
            $this->pilihanMenu = 'lihat';
            return;
        }

        if ($this->gudangTerpilih->nama === 'Mitra Abadi') {
            session()->flash('error', 'Toko Utama "Mitra Abadi" tidak dapat dihapus.');
            return;
        }

        $this->gudangTerpilih->delete();
        session()->flash('message', 'Gudang berhasil dihapus.');
        $this->reset(['gudangTerpilih']);
        $this->pilihanMenu = 'lihat';
    }


    public function batal()
    {
        $this->reset(['nama', 'lokasi', 'gudangTerpilih', 'deskripsi', 'pengurus']);
        $this->pilihanMenu = 'lihat';
        $this->resetFilter();
    }

    public function pilihMenu($menu)
    {
        $this->pilihanMenu = $menu;
    }

    public function render()
    {
        $this->filterBerdasarkanKriteria();
        return view('livewire.gudang');
    }

    public function resetFilter()
    {
        $this->kataKunci = '';
    }


    public function filterBerdasarkanKriteria()
    {
        $query = ModelGudang::query();

        if (!empty($this->kataKunci)) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->kataKunci . '%')
                    ->orWhere('lokasi', 'like', '%' . $this->kataKunci . '%')
                    ->orWhere('deskripsi', 'like', '%' . $this->kataKunci . '%')
                    ->orWhere('pengurus', 'like', '%' . $this->kataKunci . '%');
            });
        }

        $this->semuaGudang = $query->get();
    }
}
