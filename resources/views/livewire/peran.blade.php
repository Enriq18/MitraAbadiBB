<div class="container my-3">
    <!-- Tombol Navigasi -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex flex-wrap flex-md-nowrap justify-content-center gap-2">
                <button wire:click="pilihMenu('lihat')"
                    class="btn {{ $pilihanMenu == 'lihat' ? 'btn-light' : 'btn-outline-light' }}">
                    Semua peran
                </button>
                <button wire:loading class="btn btn-info">Loading..</button>
            </div>
        </div>
    </div>

    <!-- Pesan Notifikasi -->
    @if (session()->has('message'))
        <div class="alert alert-success text-center">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger text-center">{{ session('error') }}</div>
    @endif

    <!-- Konten -->
    <div class="row">
        <div class="col-12">
            @if ($pilihanMenu == 'lihat')
                <div class="card border-primary">
                    <div class="card-header text-center fs-4 fw-bold">Semua Peran</div>
                    <div class="card-body">
                        <!-- Filter -->
                        <div class="d-flex flex-wrap flex-md-nowrap gap-2 mb-3 justify-content-center">
                            <input type="text" wire:model="kataKunci" class="form-control" placeholder="Cari"
                                style="max-width: 300px;">
                            <button class="btn btn-primary" wire:click="filterBerdasarkanKriteria">Terapkan
                                Filter</button>
                            <button class="btn btn-secondary" wire:click="resetFilter">Reset</button>
                        </div>

                        <!-- Tabel -->
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-primary text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Peran</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($semuaPeran as $peran)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $peran->nama }}</td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button wire:click="pilihEdit({{ $peran->id }})"
                                                        class="btn btn-warning btn-sm">Edit</button>
                                                    <button wire:click="pilihHapus({{ $peran->id }})"
                                                        class="btn btn-danger btn-sm">Hapus</button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @elseif ($pilihanMenu == 'tambah')
                <div class="card border-success">
                    <div class="card-header bg-success text-white">Tambah Peran</div>
                    <div class="card-body">
                        <form wire:submit="simpan">
                            <label>Nama</label>
                            <input type="text" class="form-control" wire:model="nama" />
                            @error('nama')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <br>
                            <button type="submit" class="btn btn-success mt-3">SIMPAN</button>
                        </form>
                    </div>
                </div>
            @elseif ($pilihanMenu == 'edit')
                <div class="card border-warning">
                    <div class="card-header bg-warning text-white">Edit Peran</div>
                    <div class="card-body">
                        <form wire:submit="simpanEdit">
                            <label>Nama</label>
                            <input type="text" class="form-control" wire:model="nama" />
                            @error('nama')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <br>
                            <button type="submit" class="btn btn-warning mt-3">SIMPAN</button>
                        </form>
                    </div>
                </div>
            @elseif ($pilihanMenu == 'hapus')
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">Hapus Peran</div>
                    <div class="card-body">
                        <p>Anda yakin ingin menghapus peran ini?</p>
                        <p><strong>Nama:</strong> {{ $peranTerpilih->nama }}</p>
                        <button class="btn btn-danger" wire:click='hapus'>HAPUS</button>
                        <button class="btn btn-secondary" wire:click='batal'>BATAL</button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
