<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiMutasi extends Model
{

    protected $fillable = [
        'gudang_asal_id',
        'gudang_tujuan_id',
        'status',
        'tanggal_kirim',
        'tanggal_terima',
    ];


    public function mutasiItems()
    {
        return $this->hasMany(MutasiGudang::class);
    }

    public function gudangAsal()
    {
        return $this->belongsTo(Gudang::class, 'gudang_asal_id');
    }

    public function gudangTujuan()
    {
        return $this->belongsTo(Gudang::class, 'gudang_tujuan_id');
    }
}
