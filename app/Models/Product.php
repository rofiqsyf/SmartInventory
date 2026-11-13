<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'category_id',
        'satuan',
        'harga_beli',
        'harga_jual',
        'stok',
        'stok_minimum',
        'gambar',
        'deskripsi',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'harga_beli' => 'float',
            'harga_jual' => 'float',
            'stok' => 'integer',
            'stok_minimum' => 'integer',
            'status' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockTransactionDetails()
    {
        return $this->hasMany(StockTransactionDetail::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stok <= $this->stok_minimum;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stok <= 0) {
            return 'out_of_stock';
        } elseif ($this->stok <= $this->stok_minimum) {
            return 'low';
        }
        return 'normal';
    }
}

