<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_invoice',
        'tanggal',
        'user_id',
        'nama_pelanggan',
        'total_item',
        'subtotal',
        'diskon',
        'total_akhir',
        'bayar',
        'kembalian',
        'metode_pembayaran',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'datetime',
            'total_item' => 'integer',
            'subtotal' => 'float',
            'diskon' => 'float',
            'total_akhir' => 'float',
            'bayar' => 'float',
            'kembalian' => 'float',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}

