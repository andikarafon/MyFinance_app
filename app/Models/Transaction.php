<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'account_id',
        'destination_account_id',
        'category_id',
        'type',
        'amount',
        'transaction_date',
        'description',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
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
     * Relasi rekening asal.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Relasi rekening tujuan transfer.
     */
    public function destinationAccount(): BelongsTo
    {
        return $this->belongsTo(
            Account::class,
            'destination_account_id'
        );
    }

    /**
     * Relasi kategori transaksi.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope transaksi pemasukan.
     */
    public function scopeIncome(Builder $query): Builder
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope transaksi pengeluaran.
     */
    public function scopeExpense(Builder $query): Builder
    {
        return $query->where('type', 'expense');
    }

    /**
     * Scope transaksi transfer.
     */
    public function scopeTransfer(Builder $query): Builder
    {
        return $query->where('type', 'transfer');
    }
}
