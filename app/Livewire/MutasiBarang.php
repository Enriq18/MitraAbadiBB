<?php

// 1. PERBAIKAN: Update MutasiBarang.php - Tambah property dan method untuk convert nama ke ID

namespace App\Livewire;

use Livewire\Component;
use App\Models\Produk;
use App\Models\Gudang;
use App\Models\StokGudang as ModelStokGudang;
use App\Models\MutasiGudang as ModelMutasiGudang;
use App\Models\TransaksiMutasi as ModelTransaksiMutasi;
use Illuminate\Support\Facades\DB;

class MutasiBarang extends Component
{
    public $mutasiBarang = [
        ['produk_id' => '', 'produk_nama' => '', 'jumlah' => 1]
    ];

    // PERBAIKAN: Ganti property ini untuk nama gudang
    public $gudang_asal_nama, $gudang_tujuan_nama;
    public $gudang_asal_id, $gudang_tujuan_id; // Tetap ada untuk ID internal

    public $semuaProduk, $semuaGudang;
    public $riwayatMutasi = [];
    public $errorMessage;

    public function updatedMutasiBarang($value, $key)
    {
        [$index, $field] = explode('.', $key);

        if ($field === 'produk_nama') {
            $produk = Produk::where('nama', $value)->first();
            $this->mutasiBarang[$index]['produk_id'] = $produk ? $produk->id : '';
        }
    }

    // TAMBAHAN: Method untuk update gudang asal
    public function updatedGudangAsalNama()
    {
        $gudang = Gudang::where('nama', $this->gudang_asal_nama)->first();
        $this->gudang_asal_id = $gudang ? $gudang->id : null;
    }

    // TAMBAHAN: Method untuk update gudang tujuan  
    public function updatedGudangTujuanNama()
    {
        $gudang = Gudang::where('nama', $this->gudang_tujuan_nama)->first();
        $this->gudang_tujuan_id = $gudang ? $gudang->id : null;
    }

    public function mount()
    {
        $this->semuaProduk = Produk::orderBy('nama')->get();
        $this->semuaGudang = Gudang::orderBy('nama')->get();
        $this->muatRiwayat();
    }

    public function tambahBaris()
    {
        $this->mutasiBarang[] = ['produk_id' => '', 'produk_nama' => '', 'jumlah' => 1];
    }

    public function hapusBaris($index)
    {
        unset($this->mutasiBarang[$index]);
        $this->mutasiBarang = array_values($this->mutasiBarang);
    }

    public function simpanMutasi()
    {
        // PERBAIKAN: Validasi menggunakan nama gudang dan convert ke ID
        if (!$this->gudang_asal_nama || !$this->gudang_tujuan_nama) {
            $this->errorMessage = 'Gudang asal dan tujuan harus diisi.';
            return;
        }

        // Convert nama ke ID
        $gudangAsal = Gudang::where('nama', $this->gudang_asal_nama)->first();
        $gudangTujuan = Gudang::where('nama', $this->gudang_tujuan_nama)->first();

        if (!$gudangAsal) {
            $this->errorMessage = 'Gudang asal "' . $this->gudang_asal_nama . '" tidak ditemukan.';
            return;
        }

        if (!$gudangTujuan) {
            $this->errorMessage = 'Gudang tujuan "' . $this->gudang_tujuan_nama . '" tidak ditemukan.';
            return;
        }

        // Set ID untuk proses selanjutnya
        $this->gudang_asal_id = $gudangAsal->id;
        $this->gudang_tujuan_id = $gudangTujuan->id;

        if ($this->gudang_asal_id == $this->gudang_tujuan_id) {
            $this->errorMessage = 'Gudang asal dan tujuan tidak boleh sama.';
            return;
        }

        // Validasi produk tidak kosong
        $validItems = array_filter($this->mutasiBarang, function ($item) {
            return !empty($item['produk_id']) && $item['jumlah'] > 0;
        });

        if (empty($validItems)) {
            $this->errorMessage = 'Minimal harus ada satu produk yang valid.';
            return;
        }

        DB::beginTransaction();

        try {
            // Buat transaksi mutasi dengan ID yang valid
            $transaksi = ModelTransaksiMutasi::create([
                'gudang_asal_id' => $this->gudang_asal_id,
                'gudang_tujuan_id' => $this->gudang_tujuan_id,
                'status' => 'pending',
            ]);

            // Proses tiap produk
            foreach ($validItems as $item) {
                $produkId = $item['produk_id'];
                $jumlah = (int) $item['jumlah'];

                if (!$produkId || $jumlah <= 0) {
                    throw new \Exception("Produk dan jumlah harus diisi dengan benar.");
                }

                // Validasi produk ada
                $produk = Produk::find($produkId);
                if (!$produk) {
                    throw new \Exception("Produk dengan ID {$produkId} tidak ditemukan.");
                }

                // Ambil stok batch dari gudang asal (null kadaluarsa terakhir)
                $stokBatch = ModelStokGudang::where('produk_id', $produkId)
                    ->where('gudang_id', $this->gudang_asal_id)
                    ->where('stok', '>', 0)
                    ->orderByRaw('tanggal_kadaluarsa IS NULL, tanggal_kadaluarsa ASC')
                    ->get();

                $totalStokTersedia = $stokBatch->sum('stok');

                if ($totalStokTersedia < $jumlah) {
                    throw new \Exception("Stok tidak cukup untuk produk {$produk->nama}. Tersedia: {$totalStokTersedia}, Dibutuhkan: {$jumlah}");
                }

                $jumlahTersisa = $jumlah;

                foreach ($stokBatch as $stok) {
                    if ($jumlahTersisa <= 0) break;

                    $ambil = min($stok->stok, $jumlahTersisa);

                    // Kurangi stok gudang asal
                    $stok->stok -= $ambil;
                    $stok->save();

                    // Catat mutasi batch
                    ModelMutasiGudang::create([
                        'produk_id' => $produkId,
                        'jumlah' => $ambil,
                        'gudang_tujuan_id' => $this->gudang_tujuan_id,
                        'gudang_asal_id' => $this->gudang_asal_id,
                        'transaksi_mutasi_id' => $transaksi->id,
                        'tanggal_kadaluarsa' => $stok->tanggal_kadaluarsa,
                    ]);

                    $jumlahTersisa -= $ambil;
                }

                if ($jumlahTersisa > 0) {
                    throw new \Exception("Stok tidak cukup untuk produk {$produk->nama}");
                }
            }

            DB::commit();
            $this->resetForm();
            session()->flash('message', 'Mutasi berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorMessage = 'Gagal menyimpan mutasi: ' . $e->getMessage();
        }
    }

    private function resetForm()
    {
        $this->mutasiBarang = [['produk_id' => '', 'produk_nama' => '', 'jumlah' => 1]];

        // PERBAIKAN: Reset nama gudang juga
        $this->gudang_asal_nama = '';
        $this->gudang_tujuan_nama = '';
        $this->gudang_asal_id = null;
        $this->gudang_tujuan_id = null;
        $this->errorMessage = null;
        $this->muatRiwayat();
    }

    public function konfirmasiTerima($id)
    {
        $transaksi = ModelTransaksiMutasi::with('mutasiItems')->findOrFail($id);

        if ($transaksi->status === 'diterima') {
            session()->flash('message', 'Mutasi sudah dikonfirmasi sebelumnya.');
            return;
        }

        foreach ($transaksi->mutasiItems as $item) {
            $stokTujuan = ModelStokGudang::firstOrCreate([
                'produk_id' => $item->produk_id,
                'gudang_id' => $transaksi->gudang_tujuan_id,
                'tanggal_kadaluarsa' => $item->tanggal_kadaluarsa,
            ], ['stok' => 0]);

            $stokTujuan->stok += $item->jumlah;
            $stokTujuan->save();
        }

        $transaksi->update([
            'status' => 'diterima',
            'tanggal_terima' => now(),
        ]);

        session()->flash('message', 'Barang berhasil dikonfirmasi diterima.');
        $this->muatRiwayat();
    }

    protected function muatRiwayat()
    {
        $this->riwayatMutasi = ModelTransaksiMutasi::with(['gudangAsal', 'gudangTujuan', 'mutasiItems.produk'])
            ->latest()->take(10)->get();
    }

    public function render()
    {
        return view('livewire.mutasi-barang');
    }
}
