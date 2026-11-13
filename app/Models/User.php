<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }

    public function isAdminGudang(): bool
    {
        return $this->role === 'admin_gudang';
    }

    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
