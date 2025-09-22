<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StokGudang;

class Gudang extends Model
{
    protected $fillable = [
        'nama',
        'lokasi',
        'deskripsi',
        'pengurus'
    ];

    public function stok()
    {
        return $this->hasMany(StokGudang::class);
    }
}
