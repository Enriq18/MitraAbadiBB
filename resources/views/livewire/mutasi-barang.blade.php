<div class="container my-3">
    {{-- Flash message --}}
    @if (session()->has('message'))
        <div class="alert alert-success text-center">
            {{ session('message') }}
        </div>
    @endif

    @if ($errorMessage)
        <div class="alert alert-danger text-center">
            {{ $errorMessage }}
        </div>
    @endif

    {{-- Form Mutasi --}}
    <div class="card border-primary my-3">
        <div class="card-header text-center fs-4 fw-bold">Mutasi Produk</div>
        <div class="card-body">
            <form wire:submit.prevent="simpanMutasi">
                <div class="row mb-3">
                    {{-- GUDANG ASAL - PERBAIKAN: wire:model ganti ke gudang_asal_nama --}}
                    <div class="col-md-6">
                        <label>Gudang Asal</label>
                        <input list="list-gudang-asal" wire:model="gudang_asal_nama" class="form-control"
                            placeholder="Ketik nama gudang...">
                        <datalist id="list-gudang-asal">
                            @foreach ($semuaGudang as $gudang)
                                <option value="{{ $gudang->nama }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    {{-- GUDANG TUJUAN - PERBAIKAN: wire:model ganti ke gudang_tujuan_nama --}}
                    <div class="col-md-6">
                        <label>Gudang Tujuan</label>
                        <input list="list-gudang-tujuan" wire:model="gudang_tujuan_nama" class="form-control"
                            placeholder="Ketik nama gudang...">
                        <datalist id="list-gudang-tujuan">
                            @foreach ($semuaGudang as $gudang)
                                <option value="{{ $gudang->nama }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <hr>
                <div class="mb-2 fw-bold">Daftar Produk</div>

                @foreach ($mutasiBarang as $index => $item)
                    <div class="row mb-2">
                        <div class="col-md-7">
                            {{-- Search produk by nama --}}
                            <input list="list-produk-{{ $index }}" class="form-control"
                                wire:model="mutasiBarang.{{ $index }}.produk_nama"
                                placeholder="Ketik nama produk...">
                            <datalist id="list-produk-{{ $index }}">
                                @foreach ($semuaProduk as $produk)
                                    <option value="{{ $produk->nama }}"></option>
                                @endforeach
                            </datalist>
                        </div>

                        <div class="col-md-3">
                            <input type="number" class="form-control"
                                wire:model="mutasiBarang.{{ $index }}.jumlah" min="1"
                                placeholder="Jumlah">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger"
                                wire:click="hapusBaris({{ $index }})">Hapus</button>
                        </div>
                    </div>
                @endforeach

                <div class="mb-3">
                    <button type="button" class="btn btn-success mt-2" wire:click="tambahBaris">
                        + Tambah Produk
                    </button>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary">Simpan Mutasi</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Riwayat Mutasi --}}
    <div class="card border-success">
        <div class="card-header bg-success text-white text-center fw-bold">Riwayat Mutasi</div>
        <div class="card-body">
            @forelse ($riwayatMutasi as $transaksi)
                <div class="card mb-2">
                    <div class="card-body">
                        <p>
                            <strong>Dari:</strong> {{ $transaksi->gudangAsal->nama }} →
                            <strong>Ke:</strong> {{ $transaksi->gudangTujuan->nama }}
                        </p>
                        <p>
                            <strong>Status:</strong>
                            @if ($transaksi->status === 'diterima')
                                <span class="badge bg-success">Diterima</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </p>
                        <ul>
                            @foreach ($transaksi->mutasiItems as $item)
                                <li>
                                    {{ $item->produk->nama ?? '-' }} - {{ $item->jumlah }}
                                    @if ($item->tanggal_kadaluarsa)
                                        <small class="text-muted">(Exp:
                                            {{ date('d-m-Y', strtotime($item->tanggal_kadaluarsa)) }})
                                        </small>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        @if ($transaksi->status === 'pending')
                            <button class="btn btn-success btn-sm" wire:click="konfirmasiTerima({{ $transaksi->id }})">
                                Konfirmasi Diterima
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-muted text-center">Belum ada riwayat mutasi</div>
            @endforelse
        </div>
    </div>
</div>
