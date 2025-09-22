<div>
    <div class="container">
        {{-- Tombol Navigasi Menu --}}
        <div class="row my-3 justify-content-center">
            <div class="col-12 text-center d-flex flex-wrap justify-content-center gap-2">
                <button wire:click="pilihMenu('lihat')"
                    class="btn {{ $pilihanMenu == 'lihat' ? 'btn-light' : 'btn-outline-light' }}">
                    Semua Gudang
                </button>
                <button wire:click="pilihMenu('tambah')"
                    class="btn {{ $pilihanMenu == 'tambah' ? 'btn-light' : 'btn-outline-light' }}">
                    Tambah Gudang
                </button>
                <button wire:loading class="btn btn-info">Loading..</button>
            </div>
        </div>

        {{-- Notifikasi --}}
        @if (session()->has('message'))
            <div class="alert alert-success text-center">
                {{ session('message') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger text-center">
                {{ session('error') }}
            </div>
        @endif

        {{-- Konten Menu --}}
        <div class="row">
            <div class="col-12">
                @if ($pilihanMenu == 'lihat')
                    <div class="card border-primary">
                        <div class="card-header text-center fs-5 fw-bold">Daftar Gudang</div>
                        <div class="card-body">

                            <div class="row g-2 justify-content-center mb-3">
                                <div class="col-12 col-md-4">
                                    <input type="text" wire:model="kataKunci" class="form-control"
                                        placeholder="Cari">
                                </div>
                                <div class="col-6 col-md-auto">
                                    <button class="btn btn-primary w-100" wire:click="filterBerdasarkanKriteria">
                                        Terapkan Filter
                                    </button>
                                </div>
                                <div class="col-6 col-md-auto">
                                    <button class="btn btn-secondary w-100" wire:click="resetFilter">
                                        Reset
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap">
                                    <thead class="table-primary text-center">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Lokasi</th>
                                            <th>Deskripsi</th>
                                            <th>Pengurus</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($semuaGudang as $gudang)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td>{{ $gudang->nama }}</td>
                                                <td>{{ $gudang->lokasi }}</td>
                                                <td>{{ $gudang->deskripsi }}</td>
                                                <td>{{ $gudang->pengurus }}</td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button wire:click="pilihEdit({{ $gudang->id }})"
                                                            class="btn btn-warning btn-sm">Edit</button>
                                                        <button wire:click="pilihHapus({{ $gudang->id }})"
                                                            class="btn btn-danger btn-sm">Hapus</button>
                                                    </div>
                                                </td>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @elseif ($pilihanMenu == 'tambah' || $pilihanMenu == 'edit')
                    @php
                        $isEdit = $pilihanMenu == 'edit';
                        $judul = $isEdit ? 'Edit Gudang' : 'Tambah Gudang';
                        $warna = $isEdit ? 'warning' : 'success';
                    @endphp

                    <div class="card border-{{ $warna }}">
                        <div class="card-header bg-{{ $warna }} text-white text-center fw-bold">
                            {{ $judul }}
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="{{ $isEdit ? 'simpanEdit' : 'simpan' }}">
                                <div class="mb-3">
                                    <label>Nama Gudang</label>
                                    <input type="text" class="form-control" wire:model="nama">
                                    @error('nama')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label>Lokasi</label>
                                    <input type="text" class="form-control" wire:model="lokasi">
                                    @error('lokasi')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label>Deskripsi</label>
                                    <input type="text" class="form-control" wire:model="deskripsi">
                                    @error('deskripsi')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label>Pengurus</label>
                                    <input type="text" class="form-control" wire:model="pengurus">
                                    @error('pengurus')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-{{ $warna }}">SIMPAN</button>
                                    <button type="button" wire:click="batal" class="btn btn-secondary">BATAL</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif ($pilihanMenu == 'hapus')
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white text-center fw-bold">Hapus Gudang</div>
                        <div class="card-body text-center">
                            <p>Anda yakin ingin menghapus gudang?</p>
                            <p><strong>Nama:</strong> {{ $gudangTerpilih->nama }}</p>
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-danger" wire:click='hapus'>HAPUS</button>
                                <button class="btn btn-secondary" wire:click='batal'>BATAL</button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
