<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'tanggal_bergabung' => 'date',
        ];
    }

    public function savingsTransactions(): HasMany
    {
        return $this->hasMany(SavingsTransaction::class);
    }

    public function getSaldoSimpananAttribute(): string
    {
        $setoran = $this->savingsTransactions()
            ->where('jenis', 'SETORAN')
            ->sum('jumlah');
        $penarikan = $this->savingsTransactions()
            ->where('jenis', 'PENARIKAN')
            ->sum('jumlah');

        return number_format((float) $setoran - (float) $penarikan, 2, '.', '');
    }
}
