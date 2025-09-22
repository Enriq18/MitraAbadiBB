<div>
    <div class="container">
        <div class="row my-2">
            <div class="col-12 text-center">
                <button wire:click="pilihMenu('lihat')"
                    class="btn {{ $pilihanMenu == 'lihat' ? 'btn-light' : 'btn-outline-light' }}">
                    Semua Pengguna
                </button>
                <button wire:click="pilihMenu('tambah')"
                    class="btn {{ $pilihanMenu == 'tambah' ? 'btn-light' : 'btn-outline-light' }}">
                    Tambah Pengguna
                </button>
                <button wire:loading class="btn btn-info">
                    Loading..
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                @if ($pilihanMenu == 'lihat')
                    <div class="card border-primary">
                        <div class="card-header text-center fs-4 fw-bold">
                            Semua Pengguna
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-2 mb-3 justify-content-center">
                                <input type="text" wire:model="kataKunci" class="form-control" placeholder="Cari"
                                    style="width: 300px;">
                                <button class="btn btn-primary" wire:click="filterBerdasarkanKriteria">Terapkan
                                    Filter</button>
                                <button class="btn btn-secondary" wire:click="resetFilter">Reset</button>
                            </div>
                            <table class="table table-bordered">
                                <thead class="table-primary text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Peran</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($semuaPengguna as $pengguna)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $pengguna->name }}</td>
                                            <td>{{ $pengguna->email }}</td>
                                            <td>{{ $pengguna->peran->nama ?? '-' }}</td>
                                            <td class="text-center">
                                                <button wire:click="pilihEdit({{ $pengguna->id }})"
                                                    class="btn btn-warning">
                                                    Edit Pengguna
                                                </button>
                                                <button wire:click="pilihHapus({{ $pengguna->id }})"
                                                    class="btn btn-danger">
                                                    Hapus Pengguna
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @elseif ($pilihanMenu == 'tambah')
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            Tambah Pengguna
                        </div>
                        <div class="card-body">
                            <form wire:submit="simpan">
                                <label>Nama</label>
                                <input type="text" class="form-control" wire:model="nama" />
                                @error('nama')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <br>
                                <label>Email</label>
                                <input type="text" class="form-control" wire:model="email" />
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <br>
                                <label>Password</label>
                                <input type="text" class="form-control" wire:model="password" />
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <br>
                                <label>Peran</label>
                                <select class="form-control" wire:model='peran'>
                                    <option value="">Pilih Peran</option>
                                    @foreach ($daftarPeran as $id => $nama)
                                        <option value="{{ $id }}">{{ $nama }}</option>
                                    @endforeach
                                </select>

                                @error('peran')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                                <br>
                                <button type="submit" class="btn btn-success mt-3">SIMPAN</button>
                            </form>
                        </div>
                    </div>
                @elseif ($pilihanMenu == 'edit')
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-white">
                            Edit Pengguna
                        </div>
                        <div class="card-body">
                            <form wire:submit="simpanEdit">
                                <label>Nama</label>
                                <input type="text" class="form-control" wire:model="nama" />
                                @error('nama')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <br>
                                <label>Email</label>
                                <input type="text" class="form-control" wire:model="email" />
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <br>
                                <label>Password</label>
                                <input type="text" class="form-control" wire:model="password" />
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <br>
                                <label>Peran</label>
                                <select class="form-control" wire:model='peran'>
                                    <option value="">Pilih Peran</option>
                                    @foreach ($daftarPeran as $id => $nama)
                                        <option value="{{ $id }}">{{ $nama }}</option>
                                    @endforeach
                                </select>
                                @error('peran')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <br>
                                <button type="submit" class="btn btn-warning mt-3">SIMPAN</button>
                                <button type="button" wire:click='batal' class="btn btn-secondary mt-3">BATAL</button>
                            </form>
                        </div>
                    </div>
                @elseif ($pilihanMenu == 'hapus')
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            Hapus Pengguna
                        </div>
                        <div class="card-body">
                            Anda yakin ingin menghapus pengguna?
                            <p>Nama: {{ $penggunaTerpilih->name }}</p>
                            <button class="btn btn-danger" wire:click='hapus'>HAPUS</button>
                            <button class="btn btn-secondary" wire:click='batal'>BATAL</button>
                        </div>
                        @if (session()->has('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
