<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'stok_tersedia' => 'integer',
            'stok_tersisa' => 'integer',
            'harga_beli' => 'decimal:2',
            'harga_jual' => 'decimal:2',
        ];
    }
}
