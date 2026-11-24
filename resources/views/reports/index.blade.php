
<x-finance-layout>
    <x-slot name="title">
        Laporan Keuangan
    </x-slot>

    @php
        $monthNames = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $formatRupiah = fn ($value) =>
            'Rp ' . number_format((float) $value, 0, ',', '.');

        $maxCategoryIncome = max(
            1,
            (float) $incomeByCategory->max('total')
        );

        $maxCategoryExpense = max(
            1,
            (float) $expenseByCategory->max('total')
        );

        $incomePercentage = ($totalIncome + $totalExpense) > 0
            ? ($totalIncome / ($totalIncome + $totalExpense)) * 100
            : 0;

        $expensePercentage = ($totalIncome + $totalExpense) > 0
            ? ($totalExpense / ($totalIncome + $totalExpense)) * 100
            : 0;
    @endphp

    <style>
        .report-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .report-summary-card {
            padding: 22px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
        }

        .report-section {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 22px;
        }

        .report-progress-track {
            width: 100%;
            height: 10px;
            background: #f1f5f9;
            border-radius: 999px;
            overflow: hidden;
        }

        .report-progress-bar {
            height: 100%;
            border-radius: 999px;
        }

        .report-category-item {
            padding: 14px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .report-category-item:last-child {
            border-bottom: none;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
        }

        .report-table th {
            padding: 14px 16px;
            background: #f8fafc;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            text-align: left;
            white-space: nowrap;
        }

        .report-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
            color: #475569;
        }

        .report-table tbody tr:hover {
            background: #f8fafc;
        }

        .report-filter-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 10px 18px;
            border-radius: 8px;
            background: #1e293b;
            color: white;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .report-filter-button:hover {
            background: #334155;
        }

        .report-print-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 10px 18px;
            border-radius: 8px;
            background: #047857;
            color: white;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s;
        }

        .report-print-button:hover {
            background: #065f46;
        }

        .report-reset-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 10px 18px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            color: #475569;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }

        .report-reset-button:hover {
            background: #f8fafc;
        }

        @media (max-width: 767px) {
            .report-summary-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="space-y-6">

        {{-- ==========================================
             HEADER
        =========================================== --}}

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Laporan Keuangan
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Ringkasan pemasukan dan pengeluaran berdasarkan periode.
                </p>
            </div>

            
            <div class="flex flex-wrap items-center gap-2">

            {{-- EXPORT EXCEL --}}
            <a href="{{ route('reports.export', [
                'month' => $month,
                'year' => $year,
                'account_id' => $selectedAccountId,
            ]) }}"
            class="inline-flex items-center justify-center min-h-[42px] px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition">

                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5 mr-2"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 3v12m0 0 4-4m-4 4-4-4M4 17v3h16v-3" />

                </svg>

                Export Excel

            </a>

            {{-- PREVIEW CETAK --}}
            <a href="{{ route('reports.print', [
                'month' => $month,
                'year' => $year,
                'account_id' => $selectedAccountId,
            ]) }}"
            target="_blank"
            class="report-print-button">

                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5 mr-2"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v7H6z" />

                </svg>

                Preview Cetak

            </a>

            </div>


        </div>


        {{-- ==========================================
             FILTER LAPORAN
        =========================================== --}}

        <div class="report-section">

            <div class="mb-4">
                <h3 class="font-semibold text-gray-800">
                    Filter Laporan
                </h3>

                <p class="mt-1 text-xs text-gray-500">
                    Tentukan periode dan rekening yang ingin ditampilkan.
                </p>
            </div>

            <form method="GET"
                  action="{{ route('reports.index') }}"
                  class="flex flex-col gap-4 lg:flex-row lg:items-end">

                {{-- BULAN --}}
                <div class="w-full lg:w-48">

                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Bulan
                    </label>

                    <select name="month"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-slate-500 focus:ring-slate-500">

                        @foreach ($monthNames as $number => $name)

                            <option value="{{ $number }}"
                                @selected($month == $number)>
                                {{ $name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- TAHUN --}}
                <div class="w-full lg:w-32">

                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Tahun
                    </label>

                    <input type="number"
                           name="year"
                           value="{{ $year }}"
                           min="2000"
                           max="2100"
                           required
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-slate-500 focus:ring-slate-500">

                </div>


                {{-- REKENING --}}
                <div class="w-full lg:flex-1">

                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Rekening
                    </label>

                    <select name="account_id"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-slate-500 focus:ring-slate-500">

                        <option value="">
                            Semua Rekening
                        </option>

                        @foreach ($accounts as $account)

                            <option value="{{ $account->id }}"
                                @selected((string) $selectedAccountId === (string) $account->id)>

                                {{ $account->name }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- TOMBOL FILTER --}}
                <div class="flex flex-wrap gap-2">

                    <button type="submit"
                            class="report-filter-button">

                        Tampilkan

                    </button>

                    <a href="{{ route('reports.index') }}"
                       class="report-reset-button">

                        Reset

                    </a>

                </div>

            </form>

        </div>


        {{-- ==========================================
             INFORMASI PERIODE
        =========================================== --}}

        <div class="flex flex-col gap-1">

            <p class="text-sm font-semibold text-gray-800">
                Periode Laporan:
                {{ $reportPeriod }}
            </p>

            <p class="text-xs text-gray-500">
                {{ $startDate->format('d/m/Y') }}
                sampai
                {{ $endDate->format('d/m/Y') }}
            </p>

        </div>


        {{-- ==========================================
             RINGKASAN KEUANGAN
        =========================================== --}}

        <div class="report-summary-grid">

            {{-- TOTAL PEMASUKAN --}}
            <div class="report-summary-card">

                <div class="flex items-center justify-between">

                    <p class="text-sm font-medium text-gray-500">
                        Total Pemasukan
                    </p>

                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-5 h-5"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M7 17 17 7M7 7h10v10" />

                        </svg>

                    </div>

                </div>

                <h2 class="mt-4 text-2xl font-bold text-emerald-600">
                    {{ $formatRupiah($totalIncome) }}
                </h2>

                <p class="mt-2 text-xs text-gray-400">
                    Seluruh pemasukan pada periode ini
                </p>

            </div>


            {{-- TOTAL PENGELUARAN --}}
            <div class="report-summary-card">

                <div class="flex items-center justify-between">

                    <p class="text-sm font-medium text-gray-500">
                        Total Pengeluaran
                    </p>

                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-red-50 text-red-600">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-5 h-5"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M7 7 17 17M17 7v10H7" />

                        </svg>

                    </div>

                </div>

                <h2 class="mt-4 text-2xl font-bold text-red-600">
                    {{ $formatRupiah($totalExpense) }}
                </h2>

                <p class="mt-2 text-xs text-gray-400">
                    Seluruh pengeluaran pada periode ini
                </p>

            </div>


            {{-- SELISIH --}}
            <div class="report-summary-card">

                <div class="flex items-center justify-between">

                    <p class="text-sm font-medium text-gray-500">
                        Selisih Pemasukan
                    </p>

                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-slate-100 text-slate-600">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-5 h-5"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M4 12h16M12 4v16" />

                        </svg>

                    </div>

                </div>

                <h2 class="mt-4 text-2xl font-bold {{ $netTotal >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    {{ $formatRupiah($netTotal) }}
                </h2>

                <p class="mt-2 text-xs text-gray-400">
                    Pemasukan dikurangi pengeluaran
                </p>

            </div>

        </div>


        {{-- ==========================================
             PERBANDINGAN PEMASUKAN DAN PENGELUARAN
        =========================================== --}}

        <div class="report-section">

            <div class="mb-6">

                <h3 class="font-semibold text-gray-800">
                    Perbandingan Keuangan
                </h3>

                <p class="mt-1 text-sm text-gray-400">
                    Perbandingan total pemasukan dan pengeluaran periode ini.
                </p>

            </div>


            {{-- PEMASUKAN --}}
            <div class="mb-6">

                <div class="flex items-center justify-between mb-2">

                    <div class="flex items-center gap-2">

                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>

                        <span class="text-sm font-medium text-gray-600">
                            Pemasukan
                        </span>

                    </div>

                    <span class="text-sm font-semibold text-emerald-600">
                        {{ $formatRupiah($totalIncome) }}
                    </span>

                </div>

                <div class="report-progress-track">

                    <div class="report-progress-bar bg-emerald-500"
                         data-width="{{ $incomePercentage }}">
                    </div>

                </div>

            </div>


            {{-- PENGELUARAN --}}
            <div>

                <div class="flex items-center justify-between mb-2">

                    <div class="flex items-center gap-2">

                        <span class="w-3 h-3 rounded-full bg-red-400"></span>

                        <span class="text-sm font-medium text-gray-600">
                            Pengeluaran
                        </span>

                    </div>

                    <span class="text-sm font-semibold text-red-600">
                        {{ $formatRupiah($totalExpense) }}
                    </span>

                </div>

                <div class="report-progress-track">

                    <div class="report-progress-bar bg-red-400"
                         data-width="{{ $expensePercentage }}">
                    </div>

                </div>

            </div>

        </div>


        {{-- ==========================================
             ANALISIS KATEGORI
        =========================================== --}}

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">


            {{-- KATEGORI PEMASUKAN --}}
            <div class="report-section">

                <div class="mb-4">

                    <h3 class="font-semibold text-gray-800">
                        Pemasukan Berdasarkan Kategori
                    </h3>

                    <p class="mt-1 text-xs text-gray-400">
                        Distribusi pemasukan setiap kategori.
                    </p>

                </div>

                @forelse ($incomeByCategory as $item)

                    @php
                        $categoryPercentage = $totalIncome > 0
                            ? ((float) $item->total / (float) $totalIncome) * 100
                            : 0;

                        $categoryWidth = ((float) $item->total / $maxCategoryIncome) * 100;
                    @endphp

                    <div class="report-category-item">

                        <div class="flex items-center justify-between gap-3 mb-2">

                            <div class="flex items-center gap-2 min-w-0">

                                <span class="text-lg">
                                    {{ $item->category?->icon ?? '📁' }}
                                </span>

                                <span class="text-sm font-medium text-gray-700 truncate">
                                    {{ $item->category?->name ?? 'Tanpa Kategori' }}
                                </span>

                            </div>

                            <span class="text-sm font-semibold text-emerald-600 whitespace-nowrap">
                                {{ $formatRupiah($item->total) }}
                            </span>

                        </div>

                        <div class="report-progress-track">

                            <div class="report-progress-bar bg-emerald-500"
                                 data-width="{{ $categoryWidth }}">
                            </div>

                        </div>

                        <p class="mt-1 text-xs text-gray-400 text-right">
                            {{ number_format($categoryPercentage, 1, ',', '.') }}%
                            dari total pemasukan
                        </p>

                    </div>

                @empty

                    <div class="py-10 text-center">

                        <p class="text-sm text-gray-400">
                            Belum ada data pemasukan.
                        </p>

                    </div>

                @endforelse

            </div>


            {{-- KATEGORI PENGELUARAN --}}
            <div class="report-section">

                <div class="mb-4">

                    <h3 class="font-semibold text-gray-800">
                        Pengeluaran Berdasarkan Kategori
                    </h3>

                    <p class="mt-1 text-xs text-gray-400">
                        Distribusi pengeluaran setiap kategori.
                    </p>

                </div>

                @forelse ($expenseByCategory as $item)

                    @php
                        $categoryPercentage = $totalExpense > 0
                            ? ((float) $item->total / (float) $totalExpense) * 100
                            : 0;

                        $categoryWidth = ((float) $item->total / $maxCategoryExpense) * 100;
                    @endphp

                    <div class="report-category-item">

                        <div class="flex items-center justify-between gap-3 mb-2">

                            <div class="flex items-center gap-2 min-w-0">

                                <span class="text-lg">
                                    {{ $item->category?->icon ?? '📁' }}
                                </span>

                                <span class="text-sm font-medium text-gray-700 truncate">
                                    {{ $item->category?->name ?? 'Tanpa Kategori' }}
                                </span>

                            </div>

                            <span class="text-sm font-semibold text-red-600 whitespace-nowrap">
                                {{ $formatRupiah($item->total) }}
                            </span>

                        </div>

                        <div class="report-progress-track">

                            <div class="report-progress-bar bg-red-400"
                                 data-width="{{ $categoryWidth }}">
                            </div>

                        </div>

                        <p class="mt-1 text-xs text-gray-400 text-right">
                            {{ number_format($categoryPercentage, 1, ',', '.') }}%
                            dari total pengeluaran
                        </p>

                    </div>

                @empty

                    <div class="py-10 text-center">

                        <p class="text-sm text-gray-400">
                            Belum ada data pengeluaran.
                        </p>

                    </div>

                @endforelse

            </div>

        </div>


        {{-- ==========================================
             RINCIAN TRANSAKSI
        =========================================== --}}

        <div class="report-section p-0 overflow-hidden">

            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between p-5">

                <div>

                    <h3 class="font-semibold text-gray-800">
                        Rincian Transaksi
                    </h3>

                    <p class="mt-1 text-xs text-gray-400">
                        Daftar transaksi pemasukan dan pengeluaran pada periode ini.
                    </p>

                </div>

                <span class="text-xs font-medium text-gray-500">
                    {{ $totalTransactions }} transaksi
                </span>

            </div>


            <div class="overflow-x-auto">

                <table class="report-table">

                    <thead>

                        <tr>

                            <th>Tanggal</th>

                            <th>Deskripsi</th>

                            <th>Kategori</th>

                            <th>Rekening</th>

                            <th>Jenis</th>

                            <th class="text-right">
                                Nominal
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($transactions as $transaction)

                            <tr>

                                {{-- TANGGAL --}}
                                <td class="whitespace-nowrap">
                                    {{ $transaction->transaction_date->format('d/m/Y') }}
                                </td>


                                {{-- DESKRIPSI --}}
                                <td>

                                    <div class="font-medium text-gray-700">
                                        {{ $transaction->description ?: 'Tanpa deskripsi' }}
                                    </div>

                                    @if ($transaction->notes)

                                        <div class="mt-1 text-xs text-gray-400">
                                            {{ $transaction->notes }}
                                        </div>

                                    @endif

                                </td>


                                {{-- KATEGORI --}}
                                <td class="whitespace-nowrap">

                                    @if ($transaction->category)

                                        {{ $transaction->category->icon ?? '' }}
                                        {{ $transaction->category->name }}

                                    @else

                                        Tanpa Kategori

                                    @endif

                                </td>


                                {{-- REKENING --}}
                                <td class="whitespace-nowrap">
                                    {{ $transaction->account?->name ?? '-' }}
                                </td>


                                {{-- JENIS --}}
                                <td>

                                    @if ($transaction->type === 'income')

                                        <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                            Pemasukan
                                        </span>

                                    @else

                                        <span class="inline-flex rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-600">
                                            Pengeluaran
                                        </span>

                                    @endif

                                </td>


                                {{-- NOMINAL --}}
                                <td class="text-right whitespace-nowrap font-semibold">

                                    <span class="{{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">

                                        {{ $transaction->type === 'income' ? '+' : '-' }}

                                        {{ $formatRupiah($transaction->amount) }}

                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="py-12 text-center">

                                    <div class="text-sm font-medium text-gray-500">
                                        Belum ada transaksi.
                                    </div>

                                    <p class="mt-1 text-xs text-gray-400">
                                        Tidak ditemukan transaksi pada periode yang dipilih.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- PAGINATION --}}
            @if ($transactions->hasPages())

                <div class="border-t border-gray-100 px-5 py-4">

                    {{ $transactions->links() }}

                </div>

            @endif

        </div>


        {{-- ==========================================
             FOOTER INFORMASI
        =========================================== --}}

        <div class="flex flex-col gap-1 text-xs text-gray-400">

            <p>
                Total transaksi:
                {{ $totalTransactions }}
            </p>

            <p>
                Laporan ini dibuat secara otomatis melalui MyFinance App.
            </p>

        </div>

    </div>


    {{-- ==========================================
         PROGRESS BAR
    =========================================== --}}

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            document.querySelectorAll('[data-width]').forEach(function (element) {

                const width = Math.min(
                    100,
                    Math.max(0, Number(element.dataset.width) || 0)
                );

                element.style.width = width + '%';

            });

        });
    </script>

</x-finance-layout>
