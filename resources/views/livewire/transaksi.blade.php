<div class="container py-3">
    <div class="text-center mb-3">
        <div class="d-inline-flex align-items-center gap-2">
            @if (!$transaksiAktif)
                <div style="min-width: 300px;">
                    <select wire:model="gudangId" class="form-select">
                        <option value="">Pilih Gudang</option>
                        @foreach ($gudangList as $gudang)
                            <option value="{{ $gudang->id }}">{{ $gudang->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-light" wire:click='transaksiBaru'>Transaksi Baru</button>
            @else
                <button class="btn btn-danger" wire:click='batalTransaksi'>Batalkan Transaksi</button>
            @endif
            <button class="btn btn-info" wire:loading>Loading..</button>
        </div>
    </div>

    @if ($transaksiAktif)
        <div class="row g-3">
            <!-- Kiri: Daftar Produk -->
            <div class="col-lg-8">
                <div class="card border-primary">
                    <div class="card-header text-center fw-bold">
                        {{ $gudangList->where('id', $gudangId)->first()->nama ?? '-' }}
                    </div>
                    <div class="card-body">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Scan Barcode"
                                wire:model.live='kode'>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex gap-2">
                                <input list="daftar-produk" class="form-control" wire:model="produkManualNama"
                                    placeholder="Cari produk...">
                                <datalist id="daftar-produk">
                                    @foreach ($semuaProdukList as $produk)
                                        <option value="{{ $produk->nama }}"> </option>
                                    @endforeach
                                </datalist>

                                <button class="btn btn-success" wire:click="tambahProdukManual">
                                    Tambah
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-primary text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Nama</th>
                                        <th>Harga</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($semuaProduk as $produk)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $produk->produk->kode }}</td>
                                            <td>{{ $produk->produk->nama }}</td>
                                            <td class="text-end">
                                                {{ number_format($produk->produk->harga, 2, '.', ',') }}</td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center">
                                                    <input type="number" style="width: 60px"
                                                        class="form-control form-control-sm text-center"
                                                        wire:model.lazy="produkJumlah.{{ $produk->id }}"
                                                        wire:change="updateJumlah({{ $produk->id }})" min="1">
                                                </div>
                                            </td>

                                            <td class="text-end">
                                                {{ number_format($produk->produk->harga * $produk->jumlah, 2, '.', ',') }}
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-danger"
                                                    wire:click='hapusProduk({{ $produk->id }})'>Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kanan: Total dan Pembayaran -->
            <div class="col-lg-4">
                <div class="card border-primary mb-3">
                    <div class="card-header text-center fw-bold">Total Biaya</div>
                    <div class="card-body d-flex justify-content-between">
                        <span>Rp.</span>
                        <span class="fw-bold">{{ number_format($totalSemuaBelanja, 2, '.', ',') }}</span>
                    </div>
                </div>

                <div class="card border-primary mb-3">
                    <div class="card-header text-center fw-bold">Bayar</div>
                    <div class="card-body">
                        <input type="number" class="form-control" placeholder="Jumlah Bayar" wire:model.live='bayar'>
                    </div>
                </div>

                <div class="card border-primary mb-3">
                    <div class="card-header text-center fw-bold">Kembalian</div>
                    <div class="card-body d-flex justify-content-between">
                        <span>Rp.</span>
                        <span class="fw-bold">{{ number_format($kembalian, 2, '.', ',') }}</span>
                    </div>
                </div>

                @if (session()->has('message'))
                    <div class="alert alert-danger">
                        {{ session('message') }}
                    </div>
                @endif

                @if ($bayar)
                    @if ($kembalian < 0)
                        <div class="alert alert-danger">Uang Kurang</div>
                    @else
                        <button class="btn btn-success w-100" wire:click='transaksiSelesai'>Bayar</button>
                    @endif
                @endif
            </div>
        </div>
    @endif

    @if ($receipt)
        <div class="row mt-3">
            <div class="col-12">
                <div class="card border-primary">
                    <div class="card-header text-center fw-bold">Receipt</div>
                    <div class="card-body">
                        <pre id="receipt-content" class="bg-light p-2">{{ $receipt }}</pre>
                        <button class="btn btn-primary mt-2" onclick="printReceipt()">Print Receipt</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <script>
        function printReceipt() {
            let content = document.getElementById("receipt-content").innerText;
            let w = window.open("", "", "width=600,height=600");
            w.document.write("<pre>" + content + "</pre>");
            w.document.close();
            w.print();
            setTimeout(() => {
                w.close();
                location.href = "{{ route('transaksi') }}";
            }, 500);
        }
    </script>



</div>
