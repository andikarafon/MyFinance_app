<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_transaction_logs', function (Blueprint $table) {
            $table->id();

            // Reference to Recurring Schedule
            $table->foreignId('recurring_transaction_id')
                ->nullable()
                ->constrained('recurring_transactions')
                ->nullOnDelete();

            // Reference to Main Transaction
            $table->foreignId('transaction_id')
                ->nullable()
                ->constrained('transactions')
                ->nullOnDelete();

            // Account and Category Snapshots
            $table->foreignId('account_id')
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            // Snapshot Information
            $table->string('account_name', 100);
            $table->string('category_name', 100)->nullable();

            $table->string('name', 150);
            $table->string('type', 20);

            // Transaction Amount
            $table->decimal('amount', 18, 2);

            // Schedule Dates
            $table->date('scheduled_date');
            $table->date('transaction_date');

            // Generation Status
            $table->string('status', 20)
                ->default('pending');

            // Confirmation and Generation
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('generated_at')->nullable();

            // Notes
            $table->text('notes')->nullable();

            $table->timestamps();

            // Unique Constraints
            $table->unique(
                ['recurring_transaction_id', 'scheduled_date'],
                'uq_rtl_schedule_date'
            );

            $table->unique(
                'transaction_id',
                'uq_rtl_transaction'
            );

            // Indexes
            $table->index(
                ['status', 'scheduled_date'],
                'idx_rtl_status_date'
            );

            $table->index(
                'recurring_transaction_id',
                'idx_rtl_schedule'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_transaction_logs');
    }
};
