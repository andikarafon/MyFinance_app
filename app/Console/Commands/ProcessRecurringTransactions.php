<?php

namespace App\Console\Commands;

use App\Services\RecurringTransactionService;
use Illuminate\Console\Command;
use Throwable;

class ProcessRecurringTransactions extends Command
{
    /**
     * Nama command yang dijalankan melalui terminal.
     */
    protected $signature = 'recurring:process';

    /**
     * Deskripsi command.
     */
    protected $description = 'Memproses jadwal transaksi berulang yang sudah jatuh tempo';

    /**
     * Eksekusi command.
     */
    public function handle(
        RecurringTransactionService $service
    ): int {

        $this->info('Memulai pemrosesan transaksi berulang...');
        $this->newLine();

        try {
            $result = $service->processDueSchedules();

            $this->info('Pemrosesan selesai.');
            $this->newLine();

            $this->table(
                ['Keterangan', 'Jumlah'],
                [
                    ['Jadwal diproses', $result['schedules_processed']],
                    ['Transaksi berhasil dibuat', $result['generated']],
                    ['Menunggu konfirmasi', $result['pending']],
                    ['Gagal diproses', $result['failed']],
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

            $this->info(
                'Semua proses selesai tanpa kesalahan teknis.'
            );

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
