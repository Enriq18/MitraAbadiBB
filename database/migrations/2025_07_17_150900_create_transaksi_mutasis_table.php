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
        Schema::create('transaksi_mutasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gudang_asal_id')->constrained('gudangs')->onDelete('cascade');
            $table->foreignId('gudang_tujuan_id')->constrained('gudangs')->onDelete('cascade');
            $table->enum('status', ['pending', 'diterima'])->default('pending');
            $table->timestamp('tanggal_terima')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_mutasis');
    }
};
