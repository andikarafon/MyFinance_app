<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecurringTransaction extends Model
{
    use SoftDeletes;

    protected $table = 'recurring_transactions';

    protected $fillable = [
        'user_id',
        'account_id',
        'category_id',
        'type',
        'name',
        'amount',
        'frequency',
        'interval',
        'day_of_week',
        'day_of_month',
        'month_of_year',
        'start_date',
        'end_date',
        'next_run_date',
        'confirmation_required',
        'reminder_days',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'interval' => 'integer',
            'day_of_week' => 'integer',
            'day_of_month' => 'integer',
            'month_of_year' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'next_run_date' => 'date',
            'confirmation_required' => 'boolean',
            'reminder_days' => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(
            RecurringTransactionLog::class,
            'recurring_transaction_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDue($query)
    {
        return $query->whereDate('next_run_date', '<=', now()->toDateString());
    }
}
