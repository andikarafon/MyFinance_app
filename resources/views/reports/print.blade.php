{{-- ============================================================
    FILE: resources/views/reports/print.blade.php
    PREVIEW CETAK LAPORAN KEUANGAN
============================================================ --}}

@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Carbon;

    $openingBalance = (float) ($openingBalance ?? 0);

    $totalIncome = (float) $totalIncome;
    $totalExpense = (float) $totalExpense;

    $runningBalance = $openingBalance;

    $formatCurrency = function ($amount) {
        return 'Rp' . number_format((float) $amount, 0, ',', '.');
    };

    $printDate = Carbon::now('Asia/Jakarta');

    $startDate = $startDate ?? Carbon::createFromDate($year, $month, 1)->startOfMonth();
    $endDate = $endDate ?? $startDate->copy()->endOfMonth();

    $accountName = $selectedAccountId
        ? optional($accounts->firstWhere('id', $selectedAccountId))->name
        : 'Semua Rekening';

    $reportOwner = Auth::user()->name ?? '-';

    // Pastikan transaksi diurutkan berdasarkan tanggal.
    $transactions = $transactions
        ->sortBy([
            ['transaction_date', 'asc'],
            ['id', 'asc'],
        ])
        ->values();
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Keuangan - {{ $reportPeriod }}</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px;
            background: #f3f4f6;
            color: #1f2937;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
        }

        .report-container {
            width: 100%;
            max-width: 1100px;
            margin: auto;
            padding: 35px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
        }

        .report-header {
            padding-bottom: 20px;
            margin-bottom: 25px;
            border-bottom: 2px solid #e5e7eb;
        }

        .report-title {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: #111827;
        }

        .report-subtitle {
            margin-top: 7px;
            color: #6b7280;
            font-size: 13px;
        }

        .report-information {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 30px;
            margin-bottom: 25px;
        }

        .information-item {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f3f4f6;
        }

        .information-label {
            color: #6b7280;
        }

        .information-value {
            font-weight: 600;
            text-align: right;
        }

        .section-title {
            margin: 25px 0 12px;
            font-size: 15px;
            font-weight: 700;
            color: #111827;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .summary-card {
            padding: 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }

        .summary-label {
            color: #6b7280;
            font-size: 12px;
        }

        .summary-value {
            margin-top: 9px;
            font-size: 18px;
            font-weight: 700;
        }

        .income {
            color: #059669;
        }

        .expense {
            color: #dc2626;
        }

        .balance {
            color: #047857;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        thead th {
            padding: 12px 10px;
            text-align: left;
            font-weight: 700;
            color: #111827;
            border-bottom: 1px solid #d1d5db;
        }

        tbody td {
            padding: 12px 10px;
            border-bottom: 1px solid #eeeeee;
            vertical-align: middle;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row td {
            padding-top: 14px;
            padding-bottom: 14px;
            font-weight: 700;
            border-top: 1px solid #d1d5db;
            border-bottom: none;
        }

        .opening-row td {
            color: #6b7280;
            font-style: italic;
            background: #fafafa;
        }

        .final-balance {
            margin-top: 25px;
            padding: 15px 12px;
            border: 1px solid #d1d5db;
            border-radius: 12px;
        }

        .final-balance-label {
            color: #666666;
            font-size: 13px;
            font-weight: 600;
        }

        .final-balance-value {
            margin-top: 12px;
            color: #00803d;
            font-size: 24px;
            font-weight: 700;
        }

        .final-balance-note {
            margin-top: 8px;
            color: #777777;
            font-size: 12px;
        }

        .empty-row {
            padding: 25px;
            color: #9ca3af;
            text-align: center;
        }

        .print-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            max-width: 1100px;
            margin: 0 auto 15px;
        }

        .print-button {
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            background: #047857;
            color: white;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .back-button {
            padding: 10px 18px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background: white;
            color: #374151;
            text-decoration: none;
            font-size: 13px;
        }

        @media print {
            body {
                padding: 0;
                background: white;
            }

            .report-container {
                max-width: none;
                padding: 10px;
                border: none;
                border-radius: 0;
            }

            .print-actions {
                display: none;
            }

            .table-wrapper {
                overflow: visible;
            }

            tr {
                page-break-inside: avoid;
            }

            .final-balance {
                page-break-inside: avoid;
            }

            @page {
                size: A4 landscape;
                margin: 15mm;
            }
        }

        @media (max-width: 700px) {
            body {
                padding: 10px;
            }

            .report-container {
                padding: 18px;
            }

            .report-information {
                grid-template-columns: 1fr;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    {{-- TOMBOL CETAK --}}
    <div class="print-actions">

        <a href="{{ route('reports.index', request()->query()) }}"
           class="back-button">
            Kembali
        </a>

        <button type="button"
                onclick="window.print()"
                class="print-button">
            Cetak / Simpan PDF
        </button>

    </div>


    <div class="report-container">

        {{-- =====================================================
             HEADER LAPORAN
        ====================================================== --}}

        <div class="report-header">

            <h1 class="report-title">
                LAPORAN KEUANGAN
            </h1>

            <p class="report-subtitle">
                MyFinance App - Personal Finance Manager
            </p>

        </div>


        {{-- =====================================================
             INFORMASI LAPORAN
        ====================================================== --}}

        <div class="report-information">

            <div class="information-item">
                <span class="information-label">
                    Pemilik Laporan
                </span>

                <span class="information-value">
                    {{ $reportOwner }}
                </span>
            </div>

            <div class="information-item">
                <span class="information-label">
                    Periode Laporan
                </span>

                <span class="information-value">
                    {{ $reportPeriod }}
                </span>
            </div>

            <div class="information-item">
                <span class="information-label">
                    Tanggal Awal
                </span>

                <span class="information-value">
                    {{ $startDate->format('d/m/Y') }}
                </span>
            </div>

            <div class="information-item">
                <span class="information-label">
                    Tanggal Akhir
                </span>

                <span class="information-value">
                    {{ $endDate->format('d/m/Y') }}
                </span>
            </div>

            <div class="information-item">
                <span class="information-label">
                    Rekening
                </span>

                <span class="information-value">
                    {{ $accountName ?? 'Semua Rekening' }}
                </span>
            </div>

            <div class="information-item">
                <span class="information-label">
                    Tanggal Cetak
                </span>

                <span class="information-value">
                    {{ $printDate->format('d/m/Y H:i') }} WIB
                </span>
            </div>

        </div>


        {{-- =====================================================
             RINGKASAN KEUANGAN
        ====================================================== --}}

        <h3 class="section-title">
            A. Ringkasan Keuangan
        </h3>

        <div class="summary-grid">

            <div class="summary-card">

                <div class="summary-label">
                    Total Pemasukan
                </div>

                <div class="summary-value income">
                    {{ $formatCurrency($totalIncome) }}
                </div>

            </div>

            <div class="summary-card">

                <div class="summary-label">
                    Total Pengeluaran
                </div>

                <div class="summary-value expense">
                    {{ $formatCurrency($totalExpense) }}
                </div>

            </div>

            <div class="summary-card">

                <div class="summary-label">
                    Selisih Periode
                </div>

                <div class="summary-value {{ ($totalIncome - $totalExpense) >= 0 ? 'income' : 'expense' }}">
                    {{ $formatCurrency($totalIncome - $totalExpense) }}
                </div>

            </div>

        </div>


        {{-- =====================================================
             RINCIAN TRANSAKSI
        ====================================================== --}}

        <h3 class="section-title">
            B. Rincian Transaksi
        </h3>

        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>
                        <th style="width: 17%;">
                            Tanggal
                        </th>

                        <th>
                            Deskripsi
                        </th>

                        <th class="text-right" style="width: 17%;">
                            Pemasukan
                        </th>

                        <th class="text-right" style="width: 17%;">
                            Pengeluaran
                        </th>

                        <th class="text-right" style="width: 19%;">
                            Saldo
                        </th>
                    </tr>

                </thead>


                <tbody>

                    {{-- SALDO AWAL --}}
                    <tr class="opening-row">

                        <td>
                            {{ $startDate->format('d/m/Y') }}
                        </td>

                        <td>
                            Saldo Awal Periode
                        </td>

                        <td class="text-right">
                            -
                        </td>

                        <td class="text-right">
                            -
                        </td>

                        <td class="text-right">
                            {{ $formatCurrency($runningBalance) }}
                        </td>

                    </tr>


                    {{-- DATA TRANSAKSI --}}
                    @forelse($transactions as $transaction)

                        @php
                            $income = $transaction->type === 'income'
                                ? (float) $transaction->amount
                                : 0;

                            $expense = $transaction->type === 'expense'
                                ? (float) $transaction->amount
                                : 0;

                            if ($transaction->type === 'income') {
                                $runningBalance += $income;
                            } elseif ($transaction->type === 'expense') {
                                $runningBalance -= $expense;
                            }
                        @endphp

                        <tr>

                            <td>
                                {{ Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}
                            </td>

                            <td>
                                {{ $transaction->description ?: '-' }}
                            </td>

                            <td class="text-right">
                                @if($income > 0)
                                    {{ $formatCurrency($income) }}
                                @else
                                    -
                                @endif
                            </td>

                            <td class="text-right">
                                @if($expense > 0)
                                    {{ $formatCurrency($expense) }}
                                @else
                                    -
                                @endif
                            </td>

                            <td class="text-right">
                                {{ $formatCurrency($runningBalance) }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="empty-row">
                                Tidak ada transaksi pada periode ini.
                            </td>
                        </tr>

                    @endforelse


                    {{-- TOTAL --}}
                    <tr class="total-row">

                        <td></td>

                        <td>
                            Total
                        </td>

                        <td class="text-right income">
                            {{ $formatCurrency($totalIncome) }}
                        </td>

                        <td class="text-right expense">
                            {{ $formatCurrency($totalExpense) }}
                        </td>

                        <td></td>

                    </tr>

                </tbody>

            </table>

        </div>


        {{-- =====================================================
             SALDO AKHIR PERIODE
        ====================================================== --}}

        <div class="final-balance">

            <div class="final-balance-label">
                SALDO AKHIR PERIODE
            </div>

            <div class="final-balance-value">
                {{ $formatCurrency($runningBalance) }}
            </div>

            <div class="final-balance-note">
                Saldo awal + total pemasukan - total pengeluaran.
            </div>

        </div>


        {{-- =====================================================
             FOOTER
        ====================================================== --}}

        <div style="margin-top: 35px; padding-top: 15px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 11px; text-align: center;">

            Laporan ini dibuat secara otomatis melalui MyFinance App.

        </div>

    </div>

</body>
</html>