<div class="container mt-4">
    <div class="row g-4">

        <!-- Stok < 5 di Gudang MA -->
        <div class="col-12 col-md-6">
            <div class="card border-danger h-100">
                <div class="card-header text-center text-danger fw-bold">
                    Stok toko yang sudah mencapai batas minimum</div>
                <div class="card-body">
                    @if ($stokMinimGudangMA->isEmpty())
                        <p class="text-muted text-center">Tidak ada produk dengan stok minim.</p>
                    @else
                        <ul class="list-group">
                            @foreach ($stokMinimGudangMA as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $item['produk']?->nama ?? 'Produk tidak ditemukan' }}
                                    <span class="badge bg-danger">{{ $item['stok'] }}</span>
                                </li>
                            @endforeach

                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <!-- Total Stok < 10 Semua Gudang -->
        <div class="col-12 col-md-6">
            <div class="card border-warning h-100">
                <div class="card-header text-center text-warning fw-bold">
                    Stok total yang sudah mencapai batas minimum</div>
                <div class="card-body">
                    @if ($stokMinimSemuaGudang->isEmpty())
                        <p class="text-muted text-center">Semua stok aman.</p>
                    @else
                        <ul class="list-group">
                            @foreach ($stokMinimSemuaGudang as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $item['produk']?->nama ?? 'Produk tidak ditemukan' }}
                                    <span class="badge bg-warning text-dark">{{ $item['total_stok'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <!-- Grafik Perbedaan Pendapatan -->
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header text-center fs-5 fw-bold">
                    Perbedaan Pendapatan
                </div>
                <div class="card-body">
                    <canvas id="revenueDifferenceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik Statistik Penjualan Harian -->
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header text-center fs-5 fw-bold">
                    Statistik Penjualan Harian
                </div>
                <div class="card-body">
                    <canvas id="dailySalesChart"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Script Chart -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Revenue Difference Chart
        const ctxRevenue = document.getElementById('revenueDifferenceChart').getContext('2d');
        new Chart(ctxRevenue, {
            type: 'bar',
            data: {
                labels: ['Bulan Lalu', 'Bulan Ini'],
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: [{{ $lastMonthRevenue }}, {{ $currentMonthRevenue }}],
                    backgroundColor: ['#FF6384', '#36A2EB']
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Daily Sales Chart
        const ctxDaily = document.getElementById('dailySalesChart').getContext('2d');
        new Chart(ctxDaily, {
            type: 'line',
            data: {
                labels: @json(array_keys($yearlySales->toArray())),
                datasets: [{
                    label: 'Penjualan Harian (Rp)',
                    data: @json(array_values($yearlySales->toArray())),
                    backgroundColor: '#36A2EB',
                    borderColor: '#36A2EB',
                    fill: false
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
