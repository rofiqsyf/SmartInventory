<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'harga_beli',
        'harga_jual',
        'jumlah',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'harga_beli' => 'float',
            'harga_jual' => 'float',
            'jumlah' => 'integer',
            'subtotal' => 'float',
        ];
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

