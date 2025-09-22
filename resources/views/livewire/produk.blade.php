<div>
    <div class="container">
        <!-- Menu Tombol -->
        <div class="row my-3">
            <div class="col-12 d-flex flex-wrap justify-content-center gap-2">
                <button wire:click="pilihMenu('lihat')"
                    class="btn {{ $pilihanMenu == 'lihat' ? 'btn-light' : 'btn-outline-light' }}">
                    Semua produk
                </button>
                <button wire:click="pilihMenu('tambah')"
                    class="btn {{ $pilihanMenu == 'tambah' ? 'btn-light' : 'btn-outline-light' }}">
                    Tambah produk
                </button>
                <button wire:click="pilihMenu('excel')"
                    class="btn {{ $pilihanMenu == 'excel' ? 'btn-light' : 'btn-outline-light' }}">
                    Import produk
                </button>
                <button wire:loading class="btn btn-info">Loading..</button>
            </div>
        </div>

        <!-- Alert -->
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

        <!-- Konten Berdasarkan Menu -->
        <div class="row">
            <div class="col-12">
                @if ($pilihanMenu == 'lihat')
                    <div class="card border-primary">
                        <div class="card-header text-center fs-4 fw-bold">Semua produk</div>
                        <div class="card-body">
                            <div class="row justify-content-center mb-3">
                                <div class="col-md-4 col-12 mb-2">
                                    <input type="text" wire:model="kataKunci" class="form-control"
                                        placeholder="Cari">
                                </div>
                                <div class="col-auto mb-2">
                                    <button class="btn btn-primary" wire:click="filterBerdasarkanKriteria">Terapkan
                                        Filter</button>
                                </div>
                                <div class="col-auto mb-2">
                                    <button class="btn btn-secondary" wire:click="resetFilter">Reset</button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap">
                                    <thead class="table-primary text-center">
                                        <tr>
                                            <th>No</th>
                                            <th>Kode</th>
                                            <th>Nama</th>
                                            <th>Merek</th>
                                            <th>Persamaan</th>
                                            <th>Harga</th>
                                            <th>Stok Min Toko</th>
                                            <th>Stok Min Total</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($semuaProduk as $produk)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td>{{ $produk->kode }}</td>
                                                <td>{{ $produk->nama }}</td>
                                                <td>{{ $produk->merek }}</td>
                                                <td>{{ $produk->persamaan }}</td>
                                                <td>Rp {{ number_format($produk->harga, 2, ',', '.') }}</td>
                                                <td>{{ $produk->minimal_stok_toko }}</td>
                                                <td>{{ $produk->minimal_stok_toko_gudang }}</td>
                                                <td class="text-center">
                                                    <button wire:click="pilihEdit({{ $produk->id }})"
                                                        class="btn btn-warning btn-sm">Edit</button>
                                                    <button wire:click="pilihHapus({{ $produk->id }})"
                                                        class="btn btn-danger btn-sm">Hapus</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @elseif ($pilihanMenu == 'tambah' || $pilihanMenu == 'edit')
                    <div class="card border-{{ $pilihanMenu == 'tambah' ? 'success' : 'warning' }}">
                        <div class="card-header bg-{{ $pilihanMenu == 'tambah' ? 'success' : 'warning' }} text-white">
                            {{ $pilihanMenu == 'tambah' ? 'Tambah produk' : 'Edit produk' }}
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="{{ $pilihanMenu == 'tambah' ? 'simpan' : 'simpanEdit' }}">
                                <div class="mb-3">
                                    <label>Nama</label>
                                    <input type="text" class="form-control" wire:model="nama" />
                                    @error('nama')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label>Kode / Barcode</label>
                                    <input type="text" class="form-control" wire:model="kode" />
                                    @error('kode')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label>Merek</label>
                                    <input type="text" class="form-control" wire:model="merek" />
                                    @error('merek')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label>Persamaan</label>
                                    <input type="text" class="form-control" wire:model="persamaan" />
                                    @error('persamaan')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label>Harga</label>
                                    <input type="number" class="form-control" wire:model="harga" />
                                    @error('harga')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label>Minimal Stok Toko</label>
                                    <input type="number" class="form-control" wire:model="minimal_stok_toko" />
                                    @error('minimal_stok_toko')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label>Minimal Stok Toko + Gudang</label>
                                    <input type="number" class="form-control" wire:model="minimal_stok_toko_gudang" />
                                    @error('minimal_stok_toko_gudang')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <button type="submit"
                                    class="btn btn-{{ $pilihanMenu == 'tambah' ? 'success' : 'warning' }}">
                                    SIMPAN
                                </button>
                            </form>
                        </div>
                    </div>
                @elseif ($pilihanMenu == 'hapus')
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">Hapus produk</div>
                        <div class="card-body">
                            Anda yakin ingin menghapus produk?
                            <p>Nama: {{ $produkTerpilih->nama }}</p>
                            <button class="btn btn-danger" wire:click='hapus'>HAPUS</button>
                            <button class="btn btn-secondary" wire:click='batal'>BATAL</button>
                        </div>
                    </div>
                @elseif ($pilihanMenu == 'excel')
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">Import produk</div>
                        <div class="card-body">
                            <form wire:submit='importExcel'>
                                <input type="file" class="form-control mb-2" wire:model='fileExcel'>
                                <button class="btn btn-primary mt-2" type="submit">SUBMIT</button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
