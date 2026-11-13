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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice')->unique();
            $table->dateTime('tanggal');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_pelanggan')->default('Pelanggan Umum');
            $table->integer('total_item')->default(0);
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('diskon', 14, 2)->default(0);
            $table->decimal('total_akhir', 14, 2)->default(0);
            $table->decimal('bayar', 14, 2)->default(0);
            $table->decimal('kembalian', 14, 2)->default(0);
            $table->string('metode_pembayaran')->default('Tunai'); // Tunai, Transfer, QRIS
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('harga_beli', 14, 2)->default(0);
            $table->decimal('harga_jual', 14, 2)->default(0);
            $table->integer('jumlah');
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
    }
};
