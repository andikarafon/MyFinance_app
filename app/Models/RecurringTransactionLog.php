<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringTransactionLog extends Model
{
    protected $table = 'recurring_transaction_logs';

    protected $fillable = [
        'recurring_transaction_id',
        'transaction_id',
        'account_id',
        'category_id',
        'account_name',
        'category_name',
        'name',
        'type',
        'amount',
        'scheduled_date',
        'transaction_date',
        'status',
        'confirmed_at',
        'generated_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'scheduled_date' => 'date',
            'transaction_date' => 'date',
            'confirmed_at' => 'datetime',
            'generated_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function recurringTransaction(): BelongsTo
    {
        return $this->belongsTo(
            RecurringTransaction::class,
            'recurring_transaction_id'
        )->withTrashed();
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeGenerated($query)
    {
        return $query->where('status', 'generated');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
