<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Peran;

class PeranSeeder extends Seeder
{
    public function run(): void
    {
        Peran::insert([
            ['nama' => 'Admin'],
            ['nama' => 'Kasir'],
            ['nama' => 'Manager'],
        ]);
    }
}
