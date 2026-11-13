<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_transaksi',
        'tipe', // masuk, keluar
        'tanggal',
        'supplier_id',
        'user_id',
        'total_item',
        'total_nominal',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'total_item' => 'integer',
            'total_nominal' => 'float',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details()
    {
        return $this->hasMany(StockTransactionDetail::class);
    }
}

