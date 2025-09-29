<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{

    protected $table = 'produks';

    protected $fillable = [
        'nama',
        'kode',
        'merek',
        'persamaan',
    ];

    protected $casts = [
        'harga' => 'float',
        'minimal_stok_toko' => 'integer',
        'minimal_stok_toko_gudang' => 'integer',
    ];

    public function stokGudang()
    {
        return $this->hasMany(StokGudang::class);
    }

    public function mutasiGudang()
    {
        return $this->hasMany(MutasiGudang::class);
    }
}
