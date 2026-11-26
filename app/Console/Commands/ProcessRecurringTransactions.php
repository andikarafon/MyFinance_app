<?php

namespace App\Console\Commands;

use App\Services\RecurringTransactionService;
use Illuminate\Console\Command;
use Throwable;

class ProcessRecurringTransactions extends Command
{
    protected $signature = 'recurring:process
                            {--schedule-id= : ID jadwal yang akan diproses}
                            {--dry-run : Simulasi tanpa mengubah database}
                            {--all : Memproses seluruh jadwal yang jatuh tempo}';

    protected $description = 'Memproses jadwal transaksi berulang yang sudah jatuh tempo';

    public function handle(RecurringTransactionService $service): int
    {
        $scheduleIdOption = $this->option('schedule-id');
        $all = (bool) $this->option('all');
        $dryRun = (bool) $this->option('dry-run');

        // Harus memilih salah satu: schedule-id atau all.
        if (
            ($scheduleIdOption && $all)
            || (!$scheduleIdOption && !$all)
        ) {
            $this->error(
                'Tentukan salah satu opsi: --schedule-id=ID atau --all.'
            );

            return self::INVALID;
        }

        $scheduleId = null;

        if ($scheduleIdOption !== null) {
            $validatedId = filter_var(
                $scheduleIdOption,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            if ($validatedId === false) {
                $this->error('Schedule ID harus berupa angka positif.');

                return self::INVALID;
            }

            $scheduleId = (int) $validatedId;
        }

        try {
            $this->info(
                $dryRun
                    ? 'Memulai simulasi transaksi berulang...'
                    : 'Memulai pemrosesan transaksi berulang...'
            );

            $this->newLine();

            if ($dryRun) {
                $result = $service->previewDueSchedules($scheduleId);
            } else {
                $result = $service->processDueSchedules($scheduleId);
            }

            $this->table(
                ['Keterangan', 'Jumlah'],
                [
                    ['Jadwal diproses', $result['schedules_processed']],
                    ['Transaksi berhasil dibuat / calon transaksi', $result['generated']],
                    ['Menunggu konfirmasi / calon Pending', $result['pending']],
                    ['Gagal diproses / calon Failed', $result['failed']],
                    ['Duplikasi dilewati', $result['duplicates_skipped']],
                    ['Kesalahan teknis', $result['errors']],
                ]
            );

            $this->newLine();

            if ($result['errors'] > 0) {
                $this->warn(
                    'Terdapat kesalahan teknis. Periksa file log Laravel.'
                );

                return self::FAILURE;
            }

            if ($result['schedules_processed'] === 0) {
                $this->warn(
                    'Tidak ditemukan jadwal aktif yang sudah jatuh tempo.'
                );

                return self::SUCCESS;
            }

            if ($dryRun) {
                $this->info(
                    'Simulasi selesai. Tidak ada perubahan pada database.'
                );
            } else {
                $this->info(
                    'Pemrosesan transaksi berulang selesai.'
                );
            }

            return self::SUCCESS;

        } catch (Throwable $exception) {
            report($exception);

            $this->error(
                'Pemrosesan transaksi berulang gagal.'
            );

            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
