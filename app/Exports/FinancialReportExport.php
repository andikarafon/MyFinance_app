<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class FinancialReportExport implements FromArray, ShouldAutoSize, WithStyles, WithTitle
{
    protected Collection $transactions;
    protected float $openingBalance;
    protected float $totalIncome;
    protected float $totalExpense;
    protected float $closingBalance;
    protected string $period;
    protected string $accountName;

    public function __construct(
        Collection $transactions,
        float $openingBalance,
        float $totalIncome,
        float $totalExpense,
        string $period,
        string $accountName
    ) {
        $this->transactions = $transactions;
        $this->openingBalance = round($openingBalance, 2);
        $this->totalIncome = round($totalIncome, 2);
        $this->totalExpense = round($totalExpense, 2);
        $this->closingBalance = round(
            $this->openingBalance + $this->totalIncome - $this->totalExpense,
            2
        );
        $this->period = $period;
        $this->accountName = $accountName;
    }

    public function array(): array
    {
        $rows = [
            ['LAPORAN KEUANGAN MYFINANCE'],
            ['Periode', $this->period],
            ['Rekening', $this->accountName],
            ['Saldo Awal', $this->openingBalance],
            [],
            [
                'No',
                'Tanggal',
                'Deskripsi',
                'Kategori',
                'Rekening',
                'Jenis Transaksi',
                'Pemasukan',
                'Pengeluaran',
                'Saldo Berjalan',
            ],
        ];

        $runningBalance = $this->openingBalance;

        foreach ($this->transactions as $index => $transaction) {
            $income = $transaction->type === 'income'
                ? (float) $transaction->amount
                : 0;

            $expense = $transaction->type === 'expense'
                ? (float) $transaction->amount
                : 0;

                $runningBalance = round(
                    $runningBalance + $income - $expense,
                    2
                );

            $rows[] = [
                $index + 1,
                $transaction->transaction_date->format('d/m/Y'),
                $transaction->description,
                $transaction->category?->name ?? '-',
                $transaction->account?->name ?? '-',
                $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran',
                $income,
                $expense,
                $runningBalance,
            ];
        }

        $rows[] = [];
        $rows[] = [
            'TOTAL',
            '',
            '',
            '',
            '',
            '',
            $this->totalIncome,
            $this->totalExpense,
            '',
        ];

        $rows[] = [
            'SALDO AKHIR',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            $this->closingBalance,
        ];

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 16,
                ],
            ],
            6 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '334155'],
                ],
            ],
            $lastRow - 1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => 'E2E8F0'],
                ],
            ],
            $lastRow => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => 'DBEAFE'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Laporan Keuangan';
    }
}
