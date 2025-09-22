<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@php
    use App\Models\Transaksi;
    // Cek ada transaksi pending
    $adaTransaksiAktif = Transaksi::where('status', 'pending')->exists();
@endphp

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #1630c0;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #f8d124;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .sidebar .nav-link {
            font-size: 16px;
            font-weight: 500;
            color: #333;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 18px;
        }

        .sidebar .nav-link:hover {
            background: #1630c0;
            color: #fff;
            border-radius: 5px;
        }

        .sidebar .active {
            background: #1630c0;
            color: #fff;
            border-radius: 5px;
        }

        .sidebar .disabled {
            pointer-events: none;
            opacity: 0.5;
        }

        .main-content {
            margin-left: 270px;
            padding: 20px;
        }

        .nav-link {
            margin-top: 3px;
        }
    </style>
</head>

<body>
    <div id="app">
        <!-- Sidebar -->
        <div class="sidebar">
            <h4 class="text-center fw-bold mb-4">{{ config('app.name', 'Mitra Abadi') }}</h4>
            <hr>
            @if (Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isManager()))
                <a href="{{ $adaTransaksiAktif ? '#' : route('home') }}"
                    class="nav-link {{ request()->routeIs('home') ? 'active' : '' }} {{ $adaTransaksiAktif ? 'disabled' : '' }}">
                    <i class="bi bi-house-door"></i> Beranda
                </a>
            @endif

            @if (Auth::check() && Auth::user()->isAdmin())
                <a href="{{ $adaTransaksiAktif ? '#' : route('peran') }}"
                    class="nav-link {{ request()->routeIs('peran') ? 'active' : '' }} {{ $adaTransaksiAktif ? 'disabled' : '' }}">
                    <i class="bi bi-person-badge"></i> Peran
                </a>
                <a href="{{ $adaTransaksiAktif ? '#' : route('user') }}"
                    class="nav-link {{ request()->routeIs('user') ? 'active' : '' }} {{ $adaTransaksiAktif ? 'disabled' : '' }}">
                    <i class="bi bi-people"></i> Pengguna
                </a>
            @endif

            @if (Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isManager()))
                <a href="{{ $adaTransaksiAktif ? '#' : route('produk') }}"
                    class="nav-link {{ request()->routeIs('produk') ? 'active' : '' }} {{ $adaTransaksiAktif ? 'disabled' : '' }}">
                    <i class="bi bi-box-seam"></i> Produk
                </a>
                <a href="{{ $adaTransaksiAktif ? '#' : route('gudang') }}"
                    class="nav-link {{ request()->routeIs('gudang') ? 'active' : '' }} {{ $adaTransaksiAktif ? 'disabled' : '' }}">
                    <i class="bi bi-shop"></i> Gudang
                </a>
            @endif

            @if (Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isManager() || Auth::user()->isKasir()))
                <a href="{{ $adaTransaksiAktif ? '#' : route('stok-gudang') }}"
                    class="nav-link {{ request()->routeIs('stok-gudang') ? 'active' : '' }} {{ $adaTransaksiAktif ? 'disabled' : '' }}">
                    <i class="bi bi-boxes"></i> Stok Gudang
                </a>
                <a href="{{ $adaTransaksiAktif ? '#' : route('mutasi.barang') }}"
                    class="nav-link {{ request()->routeIs('mutasi.barang') ? 'active' : '' }} {{ $adaTransaksiAktif ? 'disabled' : '' }}">
                    <i class="bi bi-arrow-left-right"></i> Mutasi Produk
                </a>
                <a href="{{ route('transaksi') }}"
                    class="nav-link {{ request()->routeIs('transaksi') ? 'active' : '' }}">
                    <i class="bi bi-cash"></i> Transaksi
                </a>
                <a href="{{ $adaTransaksiAktif ? '#' : route('laporan') }}"
                    class="nav-link {{ request()->routeIs('laporan') ? 'active' : '' }} {{ $adaTransaksiAktif ? 'disabled' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i> Laporan
                </a>
            @endif

            <hr>

            @guest
                @if (Route::has('login'))
                    <a href="{{ $adaTransaksiAktif ? '#' : route('login') }}"
                        class="nav-link {{ $adaTransaksiAktif ? 'disabled' : '' }}">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </a>
                @endif
            @else
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            @endguest
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="container mt-3">
                {{ $slot }}
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script>
        window.addEventListener('lockMenu', () => {
            document.querySelectorAll('.sidebar a').forEach(el => {
                if (!el.classList.contains('active')) {
                    el.classList.add('disabled');
                    el.setAttribute('href', '#');
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</body>

</html>
