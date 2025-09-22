<?php

namespace Database\Seeders;

use App\Models\Peran;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Gudang;
use Illuminate\Support\Facades\Hash;

class DataAwal extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Peran::insert([
            ['nama' => 'Admin'],
            ['nama' => 'Kasir'],
            ['nama' => 'Manager'],
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123'),
            'peran_id' => 1
        ]);

        Gudang::insert([
            [
                'nama' => 'Mitra Abadi',
                'lokasi' => 'Jl. Sudirman Km 1',
                'deskripsi' => 'Toko Utama',
                'pengurus' => 'Riko',
            ],
            [
                'nama' => 'Gudang Platinum',
                'lokasi' => 'Jl. Sudirman Km 5',
                'deskripsi' => 'Pusat penyimpanan ban luar',
                'pengurus' => 'Budi',
            ]
        ]);
    }
}
