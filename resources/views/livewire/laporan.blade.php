<div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card border-primary mt-3">
                    <div class="card-header text-center fs-4 fw-bold">
                        Laporan Transaksi
                    </div>
                    <div class="card-body">
                        <!-- Filter Form -->
                        <div class="no-print">
                            <div class="row g-2 mb-3">
                                <div class="col-6 col-md-3">
                                    <label for="day" class="form-label">Hari</label>
                                    <select wire:model.live="day" class="form-select">
                                        <option value="">Semua</option>
                                        @for ($d = 1; $d <= 31; $d++)
                                            <option value="{{ $d }}">{{ $d }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-6 col-md-3">
                                    <label for="month" class="form-label">Bulan</label>
                                    <select wire:model.live="month" class="form-select">
                                        <option value="">Semua</option>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-6 col-md-3">
                                    <label for="year" class="form-label">Tahun</label>
                                    <select wire:model.live="year" class="form-select">
                                        <option value="">Semua</option>
                                        @for ($y = date('Y'); $y >= 2000; $y--)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-12 col-md-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-primary w-100" onclick="printLaporan()">
                                        <i class="fas fa-print"></i> Cetak
                                    </button>
                                </div>
                            </div>

                            <!-- Info Filter Aktif -->
                            @if ($day || $month || $year)
                                <div class="alert alert-success">
                                    <strong>Filter Aktif:</strong>
                                    @if ($day)
                                        Hari: {{ $day }}
                                    @endif
                                    @if ($month)
                                        Bulan: {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                    @endif
                                    @if ($year)
                                        Tahun: {{ $year }}
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Print Header -->
                        <div class="print-header">
                            @if ($day || $month || $year)
                                <p>
                                    Filter:
                                    @if ($day)
                                        Hari: {{ $day }}
                                    @endif
                                    @if ($month)
                                        Bulan: {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                    @endif
                                    @if ($year)
                                        Tahun: {{ $year }}
                                    @endif
                                </p>
                            @else
                                <p>Semua Transaksi</p>
                            @endif
                            <p>Tanggal Cetak: {{ now()->format('d F Y H:i') }}</p>
                            <hr>
                        </div>

                        <!-- Tabel Data -->
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-primary">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($semuaTransaksi as $transaksi)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                {{ $transaksi->created_at->format('d-m-Y H:i') }}
                                                ( {{ $transaksi->user->name ?? '-' }} )
                                            </td>
                                            <td>Rp. {{ number_format($transaksi->total, 2, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td colspan="2">
                                                @foreach ($transaksi->detailTransaksis as $detail)
                                                    {{ $detail->jumlah }} Bh - {{ $detail->produk->nama }} -
                                                    Rp. {{ number_format($detail->subtotal, 2, ',', '.') }}<br>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary -->
                        <div id="summary" class="mt-3" style="display: none;">
                            <div class="alert alert-success">
                                <strong>Total Transaksi: </strong>{{ $semuaTransaksi->count() }}<br>
                                <strong>Total Pendapatan: </strong>Rp.
                                {{ number_format($semuaTransaksi->sum('total'), 2, '.', ',') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        @media print {

            /* Hapus elemen yang tidak ingin dicetak */
            .no-print,
            .sidebar {
                display: none !important;
            }

            /* Sembunyikan seluruh elemen secara default */
            body * {
                visibility: hidden;
            }

            /* Tampilkan hanya container laporan */
            .container,
            .container * {
                visibility: visible;
            }

            .container {
                position: absolute;
                left: 1cm;
                top: 0;
                width: 100%;
            }


            /* Hilangkan border warna biru */
            .border-primary {
                border-color: transparent !important;
            }

            /* Cetak header dan summary */
            .print-header {
                display: block !important;
                text-align: center !important;
            }

            #summary {
                display: block !important;
            }

            .table,
            .table th,
            .table td {
                font-size: 12px !important;
            }
        }


        /* Default non-print, sembunyikan header */
        .print-header {
            display: none;
        }
    </style>


    <script>
        function printLaporan() {
            document.getElementById('summary').style.display = 'block';
            window.print();
            setTimeout(() => {
                document.getElementById('summary').style.display = 'none';
            }, 1000);
        }
    </script>
</div>
