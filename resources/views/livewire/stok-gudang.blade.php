<div>
    <div class="container-fluid px-3">
        {{-- MENU BUTTONS --}}
        <div class="row my-2">
            <div class="col-12">
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <button wire:click="pilihMenu('lihat')"
                        class="btn {{ $pilihanMenu == 'lihat' ? 'btn-light' : 'btn-outline-light' }}">
                        Semua Stok Gudang
                    </button>
                    <button wire:click="pilihMenu('lihatBatch')"
                        class="btn {{ $pilihanMenu == 'lihatBatch' ? 'btn-light' : 'btn-outline-light' }}">
                        Semua Stok + Batch
                    </button>
                    <button wire:click="pilihMenu('tambah')"
                        class="btn {{ $pilihanMenu == 'tambah' ? 'btn-light' : 'btn-outline-light' }}">
                        Tambah Stok Gudang
                    </button>
                    <button wire:loading class="btn btn-info">Loading..</button>
                </div>
            </div>
        </div>

        {{-- ISI KONTEN --}}
        <div class="row">
            <div class="col-12">
                @if ($pilihanMenu == 'lihatBatch' || $pilihanMenu == 'lihat')
                    <div class="card border-primary mb-3">
                        <div class="card-header text-center fs-5 fw-bold">
                            {{ $pilihanMenu == 'lihatBatch' ? 'Semua Stok Gudang + Batch' : 'Semua Stok Gudang' }}
                        </div>
                        <div class="card-body">
                            {{-- FILTER --}}
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

                            {{-- TABLE --}}
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap">
                                    <thead class="table-primary text-center">
                                        <tr>
                                            <th>No</th>
                                            <th>Produk</th>
                                            <th>Gudang</th>
                                            <th>Stok</th>
                                            @if ($pilihanMenu == 'lihatBatch')
                                                <th>Tanggal Kadaluwarsa</th>

                                                @if (Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isManager()))
                                                    <th>Aksi</th>
                                                @endif
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($semuaStok as $stok)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td>{{ $stok->produk->nama }}</td>
                                                <td>{{ $stok->gudang->nama }}</td>
                                                <td class="text-center">{{ $stok->stok }}</td>
                                                @if ($pilihanMenu == 'lihatBatch')
                                                    <td>
                                                        {{ $stok->tanggal_kadaluarsa ? date('d-m-Y', strtotime($stok->tanggal_kadaluarsa)) : '-' }}
                                                    </td>
                                                    @if (Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isManager()))
                                                        <td class="text-center">
                                                            <button wire:click="pilihEdit({{ $stok->id }})"
                                                                class="btn btn-warning btn-sm">Edit</button>
                                                            <button wire:click="pilihHapus({{ $stok->id }})"
                                                                class="btn btn-danger btn-sm">Hapus</button>
                                                        </td>
                                                    @endif
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                @elseif ($pilihanMenu == 'tambah' || $pilihanMenu == 'edit')
                    <div class="card border-{{ $pilihanMenu == 'tambah' ? 'success' : 'warning' }}">
                        <div
                            class="card-header bg-{{ $pilihanMenu == 'tambah' ? 'success' : 'warning' }} text-white fw-bold">
                            {{ $pilihanMenu == 'tambah' ? 'Tambah Stok Gudang' : 'Edit Stok Gudang' }}
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="{{ $pilihanMenu == 'tambah' ? 'simpan' : 'simpanEdit' }}">
                                {{-- Produk --}}
                                <div class="mb-3">
                                    <label>Nama Produk</label>
                                    @if ($pilihanMenu == 'tambah')
                                        <input list="daftar-produk" wire:model="produk_nama" class="form-control"
                                            placeholder="Cari nama produk...">
                                        <datalist id="daftar-produk">
                                            @foreach ($semuaProduk as $produk)
                                                <option value="{{ $produk->nama }}"></option>
                                            @endforeach
                                        </datalist>
                                    @else
                                        <select wire:model="produk_id" class="form-control">
                                            <option value="">Pilih produk</option>
                                            @foreach ($semuaProduk as $produk)
                                                <option value="{{ $produk->id }}">{{ $produk->nama }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                    @error('produk_nama')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    @error('produk_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Gudang --}}
                                <div class="mb-3">
                                    <label>Nama Gudang</label>
                                    @if ($pilihanMenu == 'tambah')
                                        <input list="daftar-gudang" wire:model="gudang_nama" class="form-control"
                                            placeholder="Cari nama gudang...">
                                        <datalist id="daftar-gudang">
                                            @foreach ($semuaGudang as $gudang)
                                                <option value="{{ $gudang->nama }}"></option>
                                            @endforeach
                                        </datalist>
                                    @else
                                        <select wire:model="gudang_id" class="form-control">
                                            <option value="">Pilih gudang</option>
                                            @foreach ($semuaGudang as $gudang)
                                                <option value="{{ $gudang->id }}">{{ $gudang->nama }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                    @error('gudang_nama')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    @error('gudang_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Stok --}}
                                <div class="mb-3">
                                    <label>Jumlah Stok</label>
                                    <input type="number" wire:model="stok" class="form-control" />
                                    @error('stok')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Tanggal --}}
                                <div class="mb-3">
                                    <label>Tanggal Kadaluwarsa</label>
                                    <input type="date" wire:model="tanggal_kadaluarsa" class="form-control" />
                                    @error('tanggal_kadaluarsa')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Buttons --}}
                                <div class="d-flex gap-2">
                                    <button type="submit"
                                        class="btn btn-{{ $pilihanMenu == 'tambah' ? 'success' : 'warning' }}">
                                        SIMPAN
                                    </button>
                                    <button type="button" class="btn btn-secondary"
                                        wire:click="pilihMenu('lihat')">BATAL</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif ($pilihanMenu == 'hapus')
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white fw-bold">Hapus Stok Gudang</div>
                        <div class="card-body">
                            <p>Anda yakin ingin menghapus stok ini?</p>
                            <ul>
                                <li><strong>Produk:</strong> {{ $stokTerpilih->produk->nama ?? '' }}</li>
                                <li><strong>Gudang:</strong> {{ $stokTerpilih->gudang->nama ?? '' }}</li>
                                <li><strong>Jumlah Stok:</strong> {{ $stokTerpilih->stok ?? '' }}</li>
                            </ul>
                            <div class="d-flex gap-2">
                                <button class="btn btn-danger" wire:click="hapus">HAPUS</button>
                                <button class="btn btn-secondary" wire:click="batal">BATAL</button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
