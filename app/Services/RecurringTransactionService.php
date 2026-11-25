<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\RecurringTransaction;
use App\Models\RecurringTransactionLog;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class RecurringTransactionService
{
    /**
     * Memproses seluruh jadwal aktif yang sudah jatuh tempo.
     */
    public function processDueSchedules(): array
    {
        $result = [
            'schedules_processed' => 0,
            'generated' => 0,
            'pending' => 0,
            'failed' => 0,
            'duplicates_skipped' => 0,
            'errors' => 0,
        ];

        $today = Carbon::today();

        $scheduleIds = RecurringTransaction::query()
            ->active()
            ->due()
            ->orderBy('id')
            ->pluck('id');

        foreach ($scheduleIds as $scheduleId) {
            try {
                $scheduleResult = $this->processSchedule(
                    (int) $scheduleId,
                    $today
                );

                foreach ($scheduleResult as $key => $value) {
                    $result[$key] += $value;
                }
            } catch (Throwable $exception) {
                // Kegagalan teknis tidak mengubah next_run_date.
                // Jadwal dapat dicoba kembali pada eksekusi berikutnya.
                report($exception);

                $result['errors']++;
            }
        }

        return $result;
    }

    /**
     * Memproses satu jadwal dengan penguncian database.
     */
    private function processSchedule(
        int $scheduleId,
        Carbon $today
    ): array {
        return DB::transaction(function () use ($scheduleId, $today) {

            $result = [
                'schedules_processed' => 0,
                'generated' => 0,
                'pending' => 0,
                'failed' => 0,
                'duplicates_skipped' => 0,
                'errors' => 0,
            ];

            $schedule = RecurringTransaction::query()
                ->whereKey($scheduleId)
                ->lockForUpdate()
                ->first();

            if (
                !$schedule
                || $schedule->status !== 'active'
            ) {
                return $result;
            }

            $schedule->load([
                'account',
                'category',
            ]);

            $account = Account::query()
                ->whereKey($schedule->account_id)
                ->first();

            $category = $schedule->category_id
                ? Category::query()
                    ->whereKey($schedule->category_id)
                    ->first()
                : null;

            $result['schedules_processed']++;

            $nextRunDate = Carbon::parse(
                $schedule->next_run_date
            )->startOfDay();

            $endDate = $schedule->end_date
                ? Carbon::parse($schedule->end_date)->startOfDay()
                : null;

            /*
             * CATCH-UP:
             * Proses seluruh tanggal yang sudah jatuh tempo.
             */
            while ($nextRunDate->lte($today)) {

                // Tidak boleh memproses tanggal di luar periode.
                if (
                    $endDate
                    && $nextRunDate->gt($endDate)
                ) {
                    $schedule->update([
                        'status' => 'completed',
                    ]);

                    break;
                }

                $scheduledDate = $nextRunDate->toDateString();

                /*
                 * IDEMPOTENCY:
                 * Cek apakah tanggal ini sudah memiliki log.
                 */
                $existingLog = $schedule->logs()
                    ->whereDate('scheduled_date', $scheduledDate)
                    ->exists();

                if ($existingLog) {
                    $result['duplicates_skipped']++;
                } else {

                    /*
                     * Validasi rekening dan kategori.
                     */
                    $failureReason = $this->validateReferences(
                        $schedule,
                        $account,
                        $category
                    );

                    if ($failureReason !== null) {

                        $this->createLog(
                            $schedule,
                            $account,
                            $category,
                            $scheduledDate,
                            'failed',
                            null,
                            $failureReason
                        );

                        $result['failed']++;

                    } elseif ($schedule->confirmation_required) {

                        /*
                         * MODE PERLU KONFIRMASI
                         */
                        $this->createLog(
                            $schedule,
                            $account,
                            $category,
                            $scheduledDate,
                            'pending'
                        );

                        $result['pending']++;

                    } else {

                        /*
                         * MODE OTOMATIS
                         */
                        $transaction = Transaction::create([
                            'user_id' => $schedule->user_id,
                            'account_id' => $schedule->account_id,
                            'destination_account_id' => null,
                            'category_id' => $schedule->category_id,
                            'type' => $schedule->type,
                            'amount' => $schedule->amount,
                            'transaction_date' => $scheduledDate,
                            'description' => $schedule->name,
                            'notes' => null,
                        ]);

                        $this->createLog(
                            $schedule,
                            $account,
                            $category,
                            $scheduledDate,
                            'generated',
                            $transaction->id
                        );

                        $result['generated']++;
                    }
                }

                /*
                 * Hitung tanggal kejadian berikutnya.
                 */
                $nextRunDate = $this->calculateNextRunDate(
                    $schedule,
                    $nextRunDate
                );

                /*
                 * Simpan progress setiap kejadian.
                 */
                if (
                    $endDate
                    && $nextRunDate->gt($endDate)
                ) {
                    $schedule->update([
                        'next_run_date' => $nextRunDate->toDateString(),
                        'status' => 'completed',
                    ]);

                    break;
                }

                $schedule->update([
                    'next_run_date' => $nextRunDate->toDateString(),
                ]);
            }

            return $result;

        }, 3);
    }

    /**
     * Validasi rekening dan kategori.
     */
    private function validateReferences(
        RecurringTransaction $schedule,
        ?Account $account,
        ?Category $category
    ): ?string {

        if (
            !$account
            || (int) $account->user_id !== (int) $schedule->user_id
            || !$account->is_active
        ) {
            return 'Rekening tidak tersedia atau tidak aktif.';
        }

        if (
            !$category
            || (int) $category->user_id !== (int) $schedule->user_id
            || $category->type !== $schedule->type
            || !$category->is_active
        ) {
            return 'Kategori tidak tersedia atau tidak aktif.';
        }

        return null;
    }

    /**
     * Membuat log transaksi berulang.
     */
    private function createLog(
        RecurringTransaction $schedule,
        ?Account $account,
        ?Category $category,
        string $scheduledDate,
        string $status,
        ?int $transactionId = null,
        ?string $notes = null
    ): RecurringTransactionLog {

        return $schedule->logs()->create([
            'transaction_id' => $transactionId,
            'account_id' => $schedule->account_id,
            'category_id' => $schedule->category_id,
            'account_name' => $account?->name ?? '-',
            'category_name' => $category?->name,
            'name' => $schedule->name,
            'type' => $schedule->type,
            'amount' => $schedule->amount,
            'scheduled_date' => $scheduledDate,
            'transaction_date' => $scheduledDate,
            'status' => $status,
            'confirmed_at' => null,
            'generated_at' => $status === 'generated'
                ? now()
                : null,
            'notes' => $notes,
        ]);
    }

    /**
     * Menghitung tanggal kejadian berikutnya.
     *
     * Tanggal acuan day_of_month dan month_of_year
     * tetap dipertahankan meskipun terjadi penyesuaian
     * pada bulan pendek atau tahun non-kabisat.
     */
    private function calculateNextRunDate(
        RecurringTransaction $schedule,
        Carbon $currentDate
    ): Carbon {

        $date = $currentDate->copy()->startOfDay();

        switch ($schedule->frequency) {

            case 'daily':

                return $date->addDays(
                    (int) $schedule->interval
                );

            case 'weekly':

                return $date->addWeeks(
                    (int) $schedule->interval
                );

            case 'monthly':

                $targetDay = (int) $schedule->day_of_month;

                $nextMonth = $date->copy()
                    ->startOfMonth()
                    ->addMonthsNoOverflow(
                        (int) $schedule->interval
                    );

                return $nextMonth->day(
                    min($targetDay, $nextMonth->daysInMonth)
                );

            case 'yearly':

                $targetDay = (int) $schedule->day_of_month;
                $targetMonth = (int) $schedule->month_of_year;

                $nextYear = Carbon::create(
                    $date->year + (int) $schedule->interval,
                    $targetMonth,
                    1
                );

                return $nextYear->day(
                    min($targetDay, $nextYear->daysInMonth)
                );

            default:

                throw new \InvalidArgumentException(
                    'Frekuensi transaksi tidak dikenal: '
                    . $schedule->frequency
                );
        }
    }
}
