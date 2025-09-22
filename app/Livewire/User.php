<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User as ModelUser;
use App\Models\Peran;


class User extends Component
{
    public $nama, $email, $password, $peran, $daftarPeran;
    public $pilihanMenu = 'lihat';
    public $penggunaTerpilih;
    public $kataKunci = '';
    public $semuaPengguna = [];

    public function mount()
    {
        $this->daftarPeran = Peran::pluck('nama', 'id')->toArray();
    }


    public function pilihEdit($id)
    {
        $this->penggunaTerpilih = ModelUser::findOrFail($id);
        $this->nama = $this->penggunaTerpilih->name;
        $this->email = $this->penggunaTerpilih->email;
        $this->peran = $this->penggunaTerpilih->peran_id;
        $this->pilihanMenu = "edit";
    }

    public function resetFilter()
    {
        $this->kataKunci = '';
    }

    public function filterBerdasarkanKriteria()
    {
        $query = ModelUser::query();

        if (!empty($this->kataKunci)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->kataKunci . '%')
                    ->orWhere('email', 'like', '%' . $this->kataKunci . '%');
            })
                ->orWhereHas('peran', function ($q) {
                    $q->where('nama', 'like', '%' . $this->kataKunci . '%');
                });
        }

        $this->semuaPengguna = $query->get();
    }
    public function pilihHapus($id)
    {
        $this->penggunaTerpilih = ModelUser::findOrFail($id);
        $this->pilihanMenu = "hapus";
    }

    public function batal()
    {
        $this->reset(['nama', 'email', 'password', 'peran', 'penggunaTerpilih']);
        $this->pilihanMenu = 'lihat';
    }

    public function hapus()
    {
        if ($this->penggunaTerpilih->name === 'Admin') {
            session()->flash('error', 'Pengguna "Admin" tidak dapat dihapus.');
            return;
        }

        $this->penggunaTerpilih->delete();
        session()->flash('success', 'Pengguna berhasil dihapus.');
        $this->penggunaTerpilih = null;
        $this->pilihanMenu = 'lihat';
    }


    public function simpanEdit()
    {
        $this->validate([
            'nama' => 'required',
            'email' => ['required', 'email', 'unique:users,email,' . $this->penggunaTerpilih->id],
            'peran' => 'required',
        ], [
            'nama.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format harus email',
            'email.unique' => 'Email sudah digunakan',
            'peran.required' => 'Peran harus diisi',
        ]);
        $simpan = $this->penggunaTerpilih;
        $simpan->name = $this->nama;
        $simpan->email = $this->email;
        if ($this->password) {
            $simpan->password = bcrypt($this->password);
        }
        $simpan->peran_id = $this->peran;
        $simpan->save();

        $this->reset(['nama', 'email', 'password', 'peran', 'penggunaTerpilih']);
        $this->pilihanMenu = 'lihat';
    }

    public function simpan()
    {
        $this->validate([
            'nama' => 'required',
            'email' => ['required', 'email', 'unique:users,email'],
            'peran' => 'required',
            'password' => 'required'
        ], [
            'nama.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format harus email',
            'email.unique' => 'Email sudah digunakan',
            'peran.required' => 'Peran harus diisi',
            'password.required' => 'Password harus diisi',
        ]);
        $simpan = new ModelUser();
        $simpan->name = $this->nama;
        $simpan->email = $this->email;
        $simpan->password = bcrypt($this->password);
        $simpan->peran_id = $this->peran;
        $simpan->save();

        $this->reset(['nama', 'email', 'password', 'peran']);
        $this->pilihanMenu = 'lihat';
    }

    public function pilihMenu($menu)
    {
        $this->pilihanMenu = $menu;
    }

    public function render()
    {
        $this->filterBerdasarkanKriteria();
        return view('livewire.user')->with([
            'semuaPengguna' => ModelUser::all()
        ]);
    }
}
