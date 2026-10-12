<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    protected function casts(): array
    {
        return [
            'tanggal' => 'datetime',
            'total_harga' => 'decimal:2',
        ];
    }
}
