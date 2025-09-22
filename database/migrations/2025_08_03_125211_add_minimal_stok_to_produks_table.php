<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinimalStokToProduksTable extends Migration
{
    public function up()
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->integer('minimal_stok_toko')->default(0);
            $table->integer('minimal_stok_toko_gudang')->default(0);
        });
    }

    public function down()
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn('minimal_stok_toko');
            $table->dropColumn('minimal_stok_toko_gudang');
        });
    }
}
