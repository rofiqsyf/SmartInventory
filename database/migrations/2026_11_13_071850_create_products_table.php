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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('kode_produk')->unique();
            $table->string('nama_produk');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('satuan')->default('Pcs');
            $table->decimal('harga_beli', 14, 2)->default(0);
            $table->decimal('harga_jual', 14, 2)->default(0);
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(5);
            $table->string('gambar')->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
