<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mutasi_gudangs', function (Blueprint $table) {
            $table->foreignId('transaksi_mutasi_id')->nullable()->constrained('transaksi_mutasis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mutasi_gudangs', function (Blueprint $table) {
            //
        });
    }
};
