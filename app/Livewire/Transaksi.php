<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use App\Models\StokGudang;
use App\Models\Gudang;
use App\Models\Transaksi as ModelTransaksi;
use Illuminate\Support\Facades\Auth;

class Transaksi extends Component
{
    public $kode, $total, $kembalian, $totalSemuaBelanja;
    public $transaksiAktif;
    public $receipt;
    public $bayar = 0;
    public $produkJumlah = [];
    public $gudangId;
    public $produkManualNama;
    public $semuaProdukList = [];

    public function transaksiBaru()
    {
        if (!$this->gudangId) {
            session()->flash('message', 'Pilih gudang terlebih dahulu.');
            return;
        }

        $this->resetExcept(['gudangId', 'semuaProdukList']);

        $this->transaksiAktif = new ModelTransaksi();
        $this->transaksiAktif->kode = 'INV/' . date('YmdHis');
        $this->transaksiAktif->total = 0;
        $this->transaksiAktif->status = 'pending';
        $this->transaksiAktif->gudang_id = $this->gudangId;
        $this->transaksiAktif->user_id = Auth::id();
        $this->transaksiAktif->save();


        $this->dispatch('lockMenu');
    }

    public function batalTransaksi()
    {
        if ($this->transaksiAktif) {
            // Hapus detail transaksi dan kembalikan stok
            $detailTransaksi = DetailTransaksi::where('transaksi_id', $this->transaksiAktif->id)->get();

            foreach ($detailTransaksi as $detail) {
                $stokGudang = StokGudang::where('produk_id', $detail->produk_id)
                    ->where('gudang_id', $this->transaksiAktif->gudang_id)
                    ->first();
                if ($stokGudang) {
                    $stokGudang->stok += $detail->jumlah;
                    $stokGudang->save();
                }
                $detail->delete();
            }

            // Hapus transaksi aktif
            $this->transaksiAktif->delete();
        }

        // Reset dan refresh halaman
        $this->reset();
        return redirect()->route('transaksi');
    }


    public function updateJumlah($id)
    {
        $detail = DetailTransaksi::find($id);
        if (!$detail) return;

        $jumlahBaru = $this->produkJumlah[$id] ?? $detail->jumlah;
        if ($jumlahBaru < 1) {
            $this->produkJumlah[$id] = $detail->jumlah;
            session()->flash('message', 'Jumlah minimal 1.');
            return;
        }

        $jumlahLama = $detail->jumlah;
        $selisih = $jumlahBaru - $jumlahLama;

        if ($selisih > 0) {
            // PENAMBAHAN JUMLAH - Cari stok yang tersedia (null kadaluarsa terakhir)
            $stokGudangTersedia = StokGudang::where('produk_id', $detail->produk_id)
                ->where('gudang_id', $this->gudangId)
                ->where('stok', '>', 0)
                ->orderByRaw('tanggal_kadaluarsa IS NULL, tanggal_kadaluarsa ASC')
                ->get();

            $totalStokTersedia = $stokGudangTersedia->sum('stok');

            if ($totalStokTersedia < $selisih) {
                session()->flash('message', 'Stok tidak cukup untuk menambah jumlah.');
                $this->produkJumlah[$id] = $jumlahLama;
                return;
            }

            // Kurangi stok dari batch yang tersedia (FIFO)
            $sisaYangDibutuhkan = $selisih;
            foreach ($stokGudangTersedia as $stok) {
                if ($sisaYangDibutuhkan <= 0) break;

                $ambil = min($sisaYangDibutuhkan, $stok->stok);
                $stok->stok -= $ambil;
                $stok->save();

                $sisaYangDibutuhkan -= $ambil;
            }
        } elseif ($selisih < 0) {
            // PENGURANGAN JUMLAH - Kembalikan stok
            $jumlahDikembalikan = abs($selisih);

            // Cari batch untuk dikembalikan (prioritas yang ada tanggal kadaluarsa)
            $stokGudangUntukDikembalikan = StokGudang::where('produk_id', $detail->produk_id)
                ->where('gudang_id', $this->gudangId)
                ->orderByRaw('tanggal_kadaluarsa IS NULL, tanggal_kadaluarsa ASC')
                ->first();

            if ($stokGudangUntukDikembalikan) {
                $stokGudangUntukDikembalikan->stok += $jumlahDikembalikan;
                $stokGudangUntukDikembalikan->save();
            } else {
                // Jika tidak ada batch yang ditemukan, buat batch baru
                StokGudang::create([
                    'produk_id' => $detail->produk_id,
                    'gudang_id' => $this->gudangId,
                    'stok' => $jumlahDikembalikan,
                    'tanggal_kadaluarsa' => null, // atau tanggal default
                ]);
            }
        }

        // Update detail transaksi
        $detail->jumlah = $jumlahBaru;
        $detail->subtotal = $detail->produk->harga * $jumlahBaru;
        $detail->save();

        $this->hitungUlangTotal();
    }

    // Perbaikan untuk updatedKode() juga
    public function updatedKode()
    {
        if (!$this->gudangId) {
            session()->flash('message', 'Pilih gudang terlebih dahulu.');
            return;
        }

        $produk = Produk::where('kode', $this->kode)->first();
        if (!$produk) {
            session()->flash('message', 'Produk tidak ditemukan.');
            $this->reset('kode');
            return;
        }

        // Cari stok gudang berdasarkan gudang yang dipilih (FIFO, null terakhir)
        $stokGudang = StokGudang::where('produk_id', $produk->id)
            ->where('gudang_id', $this->gudangId)
            ->where('stok', '>', 0)
            ->orderByRaw('tanggal_kadaluarsa IS NULL, tanggal_kadaluarsa ASC')
            ->first();

        if (!$stokGudang) {
            session()->flash('message', 'Stok produk tidak cukup atau tidak tersedia.');
            $this->reset('kode');
            return;
        }

        $detail = DetailTransaksi::firstOrNew([
            'transaksi_id' => $this->transaksiAktif->id,
            'produk_id' => $produk->id
        ]);
        $detail->jumlah += 1;
        $detail->subtotal = $produk->harga * $detail->jumlah;
        $detail->save();

        // Kurangi stok gudang
        $stokGudang->stok -= 1;
        $stokGudang->save();

        $this->reset('kode');
        $this->hitungUlangTotal();
    }

    // Perbaikan untuk hapusProduk()
    public function hapusProduk($id)
    {
        $detail = DetailTransaksi::find($id);

        if ($detail) {
            // Kembalikan stok ke batch yang sesuai (prioritas yang ada tanggal kadaluarsa)
            $stokGudangUntukDikembalikan = StokGudang::where('produk_id', $detail->produk_id)
                ->where('gudang_id', $this->gudangId)
                ->orderByRaw('tanggal_kadaluarsa IS NULL, tanggal_kadaluarsa ASC')
                ->first();

            if ($stokGudangUntukDikembalikan) {
                $stokGudangUntukDikembalikan->stok += $detail->jumlah;
                $stokGudangUntukDikembalikan->save();
            } else {
                // Buat batch baru jika tidak ada
                StokGudang::create([
                    'produk_id' => $detail->produk_id,
                    'gudang_id' => $this->gudangId,
                    'stok' => $detail->jumlah,
                    'tanggal_kadaluarsa' => null,
                ]);
            }

            $detail->delete();
            $this->hitungUlangTotal();
        }
    }

    public function updatedBayar()
    {
        $this->kembalian = $this->bayar > 0 ? $this->bayar - $this->totalSemuaBelanja : 0;
    }

    public function transaksiSelesai()
    {
        $this->transaksiAktif->total = $this->totalSemuaBelanja;
        $this->transaksiAktif->status = 'selesai';
        $this->transaksiAktif->save();

        $this->generateReceipt();

        // Jangan redirect dulu — biarkan receipt tampil
        // Reset sebagian kecuali receipt
        $this->resetExcept('receipt');

        // Tambahkan trigger JS untuk buka print popup otomatis
        $this->dispatch('receiptReady');
    }



    private function generateReceipt()
    {
        $lineBreak = str_repeat("-", 45) . "\n";
        $receipt  = $lineBreak;
        $receipt .= "               STRUK PEMBELIAN            \n";
        $receipt .= $lineBreak;
        $receipt .= "No Transaksi : " . $this->transaksiAktif->kode . "\n";
        $receipt .= "Tanggal      : " . now()->format('d-m-Y H:i') . "\n";
        $receipt .= "Petugas      : " . ($this->transaksiAktif->user->name ?? '-') . "\n";
        $receipt .= $lineBreak;
        $receipt .= "Produk  Qty  Harga      Subtotal\n";
        $receipt .= $lineBreak;

        $detailTransaksi = DetailTransaksi::where('transaksi_id', $this->transaksiAktif->id)->get();

        foreach ($detailTransaksi as $detail) {
            $produk = Produk::find($detail->produk_id);
            $namaProduk = wordwrap($produk->nama, 50, "\n");

            $qty = str_pad($detail->jumlah, 3, " ", STR_PAD_LEFT);
            $harga = str_pad(number_format($produk->harga, 2, ',', '.'), 10, " ", STR_PAD_LEFT);
            $subtotal = str_pad(number_format($detail->subtotal, 2, ',', '.'), 10, " ", STR_PAD_LEFT);


            $receipt .= $namaProduk . "\n";
            $receipt .= "       $qty  $harga   $subtotal\n";
        }

        $receipt .= $lineBreak;
        $receipt .= "Total     : Rp " . number_format($this->transaksiAktif->total, 2, ',', '.') . "\n";
        $receipt .= "Bayar     : Rp " . number_format($this->bayar, 2, ',', '.') . "\n";
        $receipt .= "Kembalian : Rp " . number_format($this->kembalian, 2, ',', '.') . "\n";
        $receipt .= "Status    : " . strtoupper($this->transaksiAktif->status) . "\n";
        $receipt .= $lineBreak;
        $receipt .= "     Terima Kasih atas Kunjungan Anda!     \n";
        $receipt .= "       Semoga Hari Anda Menyenangkan      \n";
        $receipt .= $lineBreak;

        $this->receipt = $receipt;
    }

    public function hitungUlangTotal()
    {
        $semuaProduk = DetailTransaksi::where('transaksi_id', $this->transaksiAktif->id)->get();
        $this->totalSemuaBelanja = $semuaProduk->sum('subtotal');

        $this->updatedBayar();
    }

    public function render()
    {
        $semuaProduk = [];
        $gudangList = Gudang::all();

        if ($this->transaksiAktif) {
            $semuaProduk = DetailTransaksi::where('transaksi_id', $this->transaksiAktif->id)->get();
            $this->totalSemuaBelanja = $semuaProduk->sum('subtotal');

            foreach ($semuaProduk as $produk) {
                $this->produkJumlah[$produk->id] = $produk->jumlah;
            }
        }

        return view('livewire.transaksi', [
            'semuaProduk' => $semuaProduk,
            'gudangList' => $gudangList,
        ]);
    }

    public function mount()
    {
        $this->semuaProdukList = Produk::all();

        $pending = ModelTransaksi::where('status', 'pending')->first();
        if ($pending) {
            $this->transaksiAktif = $pending;
            $this->gudangId = $pending->gudang_id;
        }
    }
    public function tambahProdukManual()
    {
        if (!$this->produkManualNama) {
            session()->flash('message', 'Pilih produk dulu.');
            return;
        }

        // cari produk berdasarkan nama
        $produk = Produk::where('nama', $this->produkManualNama)->first();

        if (!$produk) {
            session()->flash('message', 'Produk tidak ditemukan.');
            return;
        }

        // cek stok (prioritas yang ada tanggal kadaluarsa)
        $stokGudang = StokGudang::where('produk_id', $produk->id)
            ->where('gudang_id', $this->gudangId)
            ->where('stok', '>', 0)
            ->orderByRaw('tanggal_kadaluarsa IS NULL, tanggal_kadaluarsa ASC')
            ->first();

        if (!$stokGudang) {
            session()->flash('message', 'Stok produk tidak cukup.');
            return;
        }

        // tambah detail transaksi
        $detail = DetailTransaksi::firstOrNew([
            'transaksi_id' => $this->transaksiAktif->id,
            'produk_id' => $produk->id,
        ]);

        $detail->jumlah += 1;
        $detail->subtotal = $produk->harga * $detail->jumlah;
        $detail->save();

        // update stok
        $stokGudang->stok -= 1;
        $stokGudang->save();

        // reset input
        $this->produkManualNama = '';
    }
}
