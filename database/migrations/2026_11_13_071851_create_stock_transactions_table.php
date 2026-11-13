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
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi')->unique();
            $table->enum('tipe', ['masuk', 'keluar']);
            $table->date('tanggal');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('total_item')->default(0);
            $table->decimal('total_nominal', 14, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_transaction_id')->constrained('stock_transactions')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 14, 2)->default(0);
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transaction_details');
        Schema::dropIfExists('stock_transactions');
    }
};
