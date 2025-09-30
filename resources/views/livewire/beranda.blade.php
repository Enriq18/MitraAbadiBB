<div class="container mt-4">
    <div class="row g-4">

        <!-- Stok Minimum Gudang Terpilih -->
        <div class="col-12 col-md-6">
            <div class="card border-danger h-100">
                <div class="card-header text-danger fw-bold">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Stok Minimum di Gudang/Toko</span>
                        <select wire:model.live="gudangTerpilihId" class="form-select form-select-sm w-auto">
                            @foreach ($gudangList as $gudang)
                                <option value="{{ $gudang->id }}">{{ $gudang->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    @if ($stokMinimGudangTerpilih->isEmpty())
                        <p class="text-muted text-center">Tidak ada produk dengan stok minim.</p>
                    @else
                        <ul class="list-group">
                            @foreach ($stokMinimGudangTerpilih as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $item['produk']?->nama ?? 'Produk tidak ditemukan' }}</strong>
                                        <br>
                                        <small class="text-muted">Min: {{ $item['produk']->minimal_stok_toko }}</small>
                                    </div>
                                    <span class="badge bg-danger rounded-pill">{{ $item['stok'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <!-- Total Stok Semua Gudang -->
        <div class="col-12 col-md-6">
            <div class="card border-warning h-100">
                <div class="card-header text-center text-warning fw-bold">
                    Stok Total yang Sudah Mencapai Batas Minimum
                </div>
                <div class="card-body">
                    @if ($stokMinimSemuaGudang->isEmpty())
                        <p class="text-muted text-center">Semua stok aman.</p>
                    @else
                        <ul class="list-group">
                            @foreach ($stokMinimSemuaGudang as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $item['produk']?->nama ?? 'Produk tidak ditemukan' }}</strong>
                                        <br>
                                        <small class="text-muted">Min Total:
                                            {{ $item['produk']->minimal_stok_toko_gudang }}</small>
                                    </div>
                                    <span
                                        class="badge bg-warning text-dark rounded-pill">{{ $item['total_stok'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
