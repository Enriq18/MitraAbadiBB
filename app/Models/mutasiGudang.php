<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiGudang extends Model
{
    protected $fillable = [
        'produk_id',
        'jumlah',
        'transaksi_mutasi_id',
        'tanggal_kadaluarsa',
    ];

    public function gudangAsal()
    {
        return $this->transaksi->gudangAsal();
    }

    public function gudangTujuan()
    {
        return $this->transaksi->gudangTujuan();
    }

    public function transaksi()
    {
        return $this->belongsTo(TransaksiMutasi::class, 'transaksi_mutasi_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
