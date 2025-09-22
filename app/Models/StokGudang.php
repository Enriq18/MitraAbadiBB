<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Produk;
use App\Models\Gudang;

class StokGudang extends Model
{

    use HasFactory;

    protected $fillable = [
        'produk_id',
        'gudang_id',
        'stok',
        'tanggal_kadaluarsa',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class);
    }

    protected static function booted()
    {
        static::saving(function ($stok) {
            if ($stok->stok <= 0) {
                $stok->delete();
                return false;
            }
        });
    }
}
