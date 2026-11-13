<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransactionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_transaction_id',
        'product_id',
        'jumlah',
        'harga_satuan',
        'subtotal',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'harga_satuan' => 'float',
            'subtotal' => 'float',
        ];
    }

    public function transaction()
    {
        return $this->belongsTo(StockTransaction::class, 'stock_transaction_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

