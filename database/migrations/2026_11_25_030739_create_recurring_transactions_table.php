<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_transactions', function (Blueprint $table) {
            $table->id();

            // User
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Account dan Category
            $table->foreignId('account_id')
                ->constrained('accounts')
                ->restrictOnDelete();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->restrictOnDelete();

            // Transaction Information
            $table->string('type', 20);
            $table->string('name', 150);
            $table->decimal('amount', 18, 2);

            // Recurrence Configuration
            $table->string('frequency', 20);
            $table->unsignedInteger('interval')->default(1);

            $table->unsignedTinyInteger('day_of_week')
                ->nullable();

            $table->unsignedTinyInteger('day_of_month')
                ->nullable();

            $table->unsignedTinyInteger('month_of_year')
                ->nullable();

            // Schedule Dates
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('next_run_date');

            // Confirmation and Reminder
            $table->boolean('confirmation_required')
                ->default(false);

            $table->unsignedTinyInteger('reminder_days')
                ->default(1);

            // Schedule Status
            $table->string('status', 20)
                ->default('active');

            // Timestamps
            $table->timestamps();

            // Soft Delete
            $table->softDeletes();

            // Indexes
            $table->index(
                ['user_id', 'status'],
                'idx_rt_user_status'
            );

            $table->index(
                ['status', 'next_run_date'],
                'idx_rt_status_date'
            );

            $table->index(
                'account_id',
                'idx_rt_account'
            );

            $table->index(
                'category_id',
                'idx_rt_category'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_transactions');
    }
};
