<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'bank_name',
        'account_number',
        'opening_balance',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi ke User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi seluruh transaksi berdasarkan rekening asal.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Relasi transaksi transfer masuk.
     */
    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(
            Transaction::class,
            'destination_account_id'
        );
    }

    /**
     * Menghitung saldo aktual rekening.
     *
     * Saldo akhir =
     * Saldo awal
     * + Pemasukan
     * - Pengeluaran
     * + Transfer masuk
     * - Transfer keluar
     */
    public function getCurrentBalanceAttribute(): float
    {
        $openingBalance = (float) $this->opening_balance;

        $totalIncome = $this->transactions()
            ->where('type', 'income')
            ->sum('amount');

        $totalExpense = $this->transactions()
            ->where('type', 'expense')
            ->sum('amount');

        $totalTransferOut = $this->transactions()
            ->where('type', 'transfer')
            ->sum('amount');

        $totalTransferIn = $this->incomingTransfers()
            ->where('type', 'transfer')
            ->sum('amount');

        return $openingBalance
            + (float) $totalIncome
            - (float) $totalExpense
            + (float) $totalTransferIn
            - (float) $totalTransferOut;
    }
}
