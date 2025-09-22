<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Peran as ModelPeran;

class Peran extends Component
{
    public $pilihanMenu = 'lihat';
    public $nama;
    public $peranTerpilih;
    public $semuaPeran = [];
    public $kataKunci = '';


    public function mount()
    {
        $this->ambilSemuaPeran();
    }

    public function ambilSemuaPeran()
    {
        $this->semuaPeran = ModelPeran::all();
    }

    public function pilihMenu($menu)
    {
        $this->reset(['nama', 'peranTerpilih']);
        $this->pilihanMenu = $menu;
        if ($menu == 'lihat') {
            $this->ambilSemuaPeran();
        }
    }

    public function simpan()
    {
        $this->validate([
            'nama' => 'required|unique:perans,nama'
        ], [
            'nama.required' => 'Nama harus diisi',
            'nama.unique' => 'Nama peran sudah digunakan'
        ]);

        ModelPeran::create([
            'nama' => $this->nama,
        ]);

        $this->reset(['nama']);
        $this->pilihanMenu = 'lihat';
        $this->ambilSemuaPeran();
    }

    public function pilihEdit($id)
    {
        $this->peranTerpilih = ModelPeran::findOrFail($id);
        $this->nama = $this->peranTerpilih->nama;
        $this->pilihanMenu = 'edit';
    }

    public function simpanEdit()
    {
        $this->validate([
            'nama' => 'required|unique:perans,nama,' . $this->peranTerpilih->id
        ], [
            'nama.required' => 'Nama harus diisi',
            'nama.unique' => 'Nama peran sudah digunakan'
        ]);

        $this->peranTerpilih->update([
            'nama' => $this->nama,
        ]);

        $this->reset(['nama', 'peranTerpilih']);
        $this->pilihanMenu = 'lihat';
        $this->ambilSemuaPeran();
    }

    public function pilihHapus($id)
    {
        $this->peranTerpilih = ModelPeran::findOrFail($id);
        $this->pilihanMenu = 'hapus';
    }

    public function hapus()
    {
        if ($this->peranTerpilih->pengguna()->exists()) {
            session()->flash('error', 'Peran ini tidak dapat dihapus karena masih digunakan oleh pengguna.');
            $this->reset(['peranTerpilih']);
            $this->pilihanMenu = 'lihat';
            return;
        }

        $this->peranTerpilih->delete();
        session()->flash('message', 'Peran berhasil dihapus.');
        $this->reset(['peranTerpilih']);
        $this->pilihanMenu = 'lihat';
        $this->ambilSemuaPeran();
    }


    public function batal()
    {
        $this->reset(['nama', 'peranTerpilih']);
        $this->pilihanMenu = 'lihat';
        $this->ambilSemuaPeran();
    }

    public function render()
    {
        return view('livewire.peran');
    }


    public function resetFilter()
    {
        $this->kataKunci = '';
    }


    public function filterBerdasarkanKriteria()
    {
        $query = ModelPeran::query();

        if (!empty($this->kataKunci)) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->kataKunci . '%');
            });
        }

        $this->semuaPeran = $query->get();
    }
}
