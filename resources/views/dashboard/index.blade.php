
<x-finance-layout>
    <x-slot name="title">
        Dashboard
    </x-slot>

    @php
        $formatCurrency = fn ($amount) =>
            'Rp ' . number_format((float) $amount, 0, ',', '.');

        $currentMonth = now()->translatedFormat('F Y');
    @endphp

    <style>
        /* =========================================
           SUMMARY ANGGARAN
        ========================================= */

        .dashboard-budget-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            width: 100%;
            margin-bottom: 24px;
        }

        .dashboard-budget-summary-card {
            min-width: 0;
            padding: 18px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background-color: #ffffff;
        }

        .dashboard-budget-summary-label {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 8px;
        }

        .dashboard-budget-summary-value {
            font-size: 22px;
            font-weight: 700;
            line-height: 1.4;
        }

        /* =========================================
           PROGRESS BAR
        ========================================= */

        .dashboard-progress-track {
            width: 100%;
            height: 10px;
            background-color: #f3f4f6;
            border-radius: 999px;
            overflow: hidden;
        }

        .dashboard-progress-fill {
            display: block;
            width: 0;
            height: 100%;
            border-radius: 999px;
            transition: width 0.4s ease;
        }

        /* =========================================
           RESPONSIVE
        ========================================= */

        @media (max-width: 640px) {
            .dashboard-budget-summary-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
        }
    </style>

    <div class="space-y-6">

        {{-- ========================================= --}}
        {{-- SUMMARY CARDS --}}
        {{-- ========================================= --}}

        <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px;">

            {{-- TOTAL SALDO --}}
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">

                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        Total Saldo
                    </p>

                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <span class="text-xl">▣</span>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-gray-800 mt-3">
                    {{ $formatCurrency($totalBalance) }}
                </h2>

                <p class="text-xs text-gray-400 mt-2">
                    Seluruh rekening aktif
                </p>

            </div>


            {{-- TOTAL PEMASUKAN --}}
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">

                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        Total Pemasukan
                    </p>

                    <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <span class="text-xl">↗</span>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-emerald-600 mt-3">
                    {{ $formatCurrency($totalIncome) }}
                </h2>

                <p class="text-xs text-gray-400 mt-2">
                    {{ $currentMonth }}
                </p>

            </div>


            {{-- TOTAL PENGELUARAN --}}
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">

                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        Total Pengeluaran
                    </p>

                    <div class="w-10 h-10 rounded-lg bg-red-50 text-red-500 flex items-center justify-center">
                        <span class="text-xl">↘</span>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-red-500 mt-3">
                    {{ $formatCurrency($totalExpense) }}
                </h2>

                <p class="text-xs text-gray-400 mt-2">
                    {{ $currentMonth }}
                </p>

            </div>


            {{-- JUMLAH REKENING --}}
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">

                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        Jumlah Rekening
                    </p>

                    <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <span class="text-xl">▤</span>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-indigo-600 mt-3">
                    {{ number_format($totalAccounts, 0, ',', '.') }}
                </h2>

                <p class="text-xs text-gray-400 mt-2">
                    Rekening aktif
                </p>

            </div>

        </div>


        {{-- ========================================= --}}
        {{-- RINGKASAN DAN NOTIFIKASI ANGGARAN --}}
        {{-- ========================================= --}}

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">

                <div>
                    <h3 class="font-semibold text-gray-800">
                        Monitoring Anggaran Bulanan
                    </h3>

                    <p class="text-sm text-gray-400 mt-1">
                        {{ now()->translatedFormat('F Y') }}
                    </p>
                </div>

                <a href="{{ route('budgets.index') }}"
                   class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                    Lihat Anggaran →
                </a>

            </div>


            {{-- SUMMARY ANGGARAN --}}

            <div class="dashboard-budget-summary-grid">

                {{-- TOTAL ANGGARAN --}}
                <div class="dashboard-budget-summary-card">

                    <p class="dashboard-budget-summary-label">
                        Total Anggaran
                    </p>

                    <p class="dashboard-budget-summary-value" style="color: #1e293b;">
                        {{ $formatCurrency($totalBudgetDashboard) }}
                    </p>

                    <p style="font-size: 11px; color: #94a3b8; margin-top: 6px;">
                        Batas anggaran bulan ini
                    </p>

                </div>


                {{-- TOTAL REALISASI --}}
                <div class="dashboard-budget-summary-card">

                    <p class="dashboard-budget-summary-label">
                        Total Realisasi
                    </p>

                    <p class="dashboard-budget-summary-value" style="color: #dc2626;">
                        {{ $formatCurrency($totalBudgetRealization) }}
                    </p>

                    <p style="font-size: 11px; color: #94a3b8; margin-top: 6px;">
                        Pengeluaran tercatat
                    </p>

                </div>


                {{-- SISA ANGGARAN --}}
                <div class="dashboard-budget-summary-card">

                    <p class="dashboard-budget-summary-label">
                        Sisa Anggaran
                    </p>

                    <p class="dashboard-budget-summary-value {{ $totalBudgetRemaining < 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $formatCurrency($totalBudgetRemaining) }}
                    </p>

                    <p style="font-size: 11px; color: #94a3b8; margin-top: 6px;">
                        Anggaran dikurangi realisasi
                    </p>

                </div>

            </div>


            {{-- NOTIFIKASI --}}

            @if ($budgetAlerts->isNotEmpty())

                <div class="space-y-4">

                    @foreach ($budgetAlerts as $budget)

                        @php
                            $percentage = (float) $budget->percentage;
                            $progressWidth = min(max($percentage, 0), 100);

                            if ($percentage >= 100) {
                                $statusText = 'Melebihi Anggaran';
                                $statusColor = 'text-red-700 bg-red-50';
                                $progressColor = '#ef4444';
                            } elseif ($percentage >= 80) {
                                $statusText = 'Mendekati Batas';
                                $statusColor = 'text-yellow-700 bg-yellow-50';
                                $progressColor = '#eab308';
                            } else {
                                $statusText = 'Aman';
                                $statusColor = 'text-green-700 bg-green-50';
                                $progressColor = '#22c55e';
                            }
                        @endphp

                        <div class="border border-gray-100 rounded-lg p-4">

                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">

                                <div>
                                    <p class="font-medium text-sm text-gray-800">
                                        {{ $budget->category->icon ?? '📁' }}
                                        {{ $budget->category->name ?? 'Kategori tidak tersedia' }}
                                    </p>

                                    <p class="text-xs text-gray-500 mt-1">
                                        Realisasi:
                                        {{ $formatCurrency($budget->realization) }}
                                        dari
                                        {{ $formatCurrency($budget->amount) }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-3">

                                    <span class="text-sm font-semibold text-gray-700">
                                        {{ number_format($percentage, 1, ',', '.') }}%
                                    </span>

                                    <span class="rounded-full px-3 py-1 text-xs font-medium {{ $statusColor }}">
                                        {{ $statusText }}
                                    </span>

                                </div>

                            </div>


                            {{-- PROGRESS BAR --}}

                            <div class="dashboard-progress-track mt-3">

                                <div
                                    class="dashboard-progress-fill js-dashboard-progress"
                                    data-progress="{{ number_format($progressWidth, 2, '.', '') }}"
                                    data-color="{{ $progressColor }}"
                                ></div>

                            </div>


                            <div class="flex justify-between mt-2 text-xs text-gray-400">

                                <span>
                                    Sisa:
                                    {{ $formatCurrency($budget->remaining) }}
                                </span>

                                <span>
                                    Anggaran:
                                    {{ $formatCurrency($budget->amount) }}
                                </span>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="rounded-lg border border-dashed border-gray-300 py-8 text-center">

                    <div class="text-3xl mb-2">
                        📊
                    </div>

                    <p class="font-medium text-gray-700">
                        Belum Ada Anggaran
                    </p>

                    <p class="text-sm text-gray-400 mt-1">
                        Belum ada anggaran yang dibuat untuk bulan ini.
                    </p>

                    <a href="{{ route('budgets.create') }}"
                       class="inline-block mt-4 rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                        Tambah Anggaran
                    </a>

                </div>

            @endif

        </div>


        {{-- ========================================= --}}
        {{-- GRAFIK DAN SALDO REKENING --}}
        {{-- ========================================= --}}

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

            {{-- GRAFIK KEUANGAN --}}
            <div class="xl:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                    <div>
                        <h3 class="font-semibold text-gray-800">
                            Grafik Keuangan
                        </h3>

                        <p class="text-sm text-gray-400 mt-1">
                            Pemasukan dan pengeluaran enam bulan terakhir
                        </p>
                    </div>


                    {{-- LEGEND --}}

                    <div class="flex items-center gap-4 text-xs">

                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-sm bg-emerald-500"></span>
                            <span class="text-gray-500">Pemasukan</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-sm bg-red-400"></span>
                            <span class="text-gray-500">Pengeluaran</span>
                        </div>

                    </div>

                </div>


                {{-- CHART DATA --}}

                <div class="mt-8 space-y-6">

                    @foreach($chartData as $item)

                        @php
                            $incomeHeight = $chartMax > 0
                                ? ($item['income'] / $chartMax) * 100
                                : 0;

                            $expenseHeight = $chartMax > 0
                                ? ($item['expense'] / $chartMax) * 100
                                : 0;

                            $incomeBarWidth = min(max($incomeHeight, 0), 100);
                            $expenseBarWidth = min(max($expenseHeight, 0), 100);
                        @endphp

                        <div class="space-y-2">

                            {{-- BULAN --}}

                            <div class="text-xs font-semibold text-gray-700">
                                {{ $item['month'] }}
                            </div>


                            {{-- PEMASUKAN --}}

                            <div class="flex items-center gap-3">

                                <div class="w-24 text-xs text-gray-500 shrink-0">
                                    Pemasukan
                                </div>

                                <div style="flex: 1; height: 16px; background-color: #f3f4f6; border-radius: 999px; overflow: hidden;">

                                    <div
                                        class="dashboard-progress-fill js-dashboard-progress"
                                        data-progress="{{ number_format($incomeBarWidth, 2, '.', '') }}"
                                        data-color="#10b981"
                                        data-min-width="{{ $item['income'] > 0 ? '4' : '0' }}"
                                    ></div>

                                </div>

                                <div class="w-28 text-right text-xs font-medium text-gray-600 shrink-0">
                                    {{ $formatCurrency($item['income']) }}
                                </div>

                            </div>


                            {{-- PENGELUARAN --}}

                            <div class="flex items-center gap-3">

                                <div class="w-24 text-xs text-gray-500 shrink-0">
                                    Pengeluaran
                                </div>

                                <div style="flex: 1; height: 16px; background-color: #f3f4f6; border-radius: 999px; overflow: hidden;">

                                    <div
                                        class="dashboard-progress-fill js-dashboard-progress"
                                        data-progress="{{ number_format($expenseBarWidth, 2, '.', '') }}"
                                        data-color="#f87171"
                                        data-min-width="{{ $item['expense'] > 0 ? '4' : '0' }}"
                                    ></div>

                                </div>

                                <div class="w-28 text-right text-xs font-medium text-gray-600 shrink-0">
                                    {{ $formatCurrency($item['expense']) }}
                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>


                {{-- EMPTY CHART --}}

                @if($chartData->sum('income') == 0 && $chartData->sum('expense') == 0)

                    <div class="text-center text-xs text-gray-400 mt-6">
                        Belum ada data pemasukan atau pengeluaran.
                    </div>

                @endif

            </div>


            {{-- ========================================= --}}
            {{-- SALDO REKENING --}}
            {{-- ========================================= --}}

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

                <div class="flex items-center justify-between">

                    <div>
                        <h3 class="font-semibold text-gray-800">
                            Saldo Rekening
                        </h3>

                        <p class="text-sm text-gray-400 mt-1">
                            Saldo masing-masing rekening
                        </p>
                    </div>

                    <a href="{{ route('accounts.index') }}"
                       class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                        Lihat Semua
                    </a>

                </div>


                <div class="mt-5 space-y-4">

                    @forelse($accounts as $account)

                        <div class="pb-4 border-b border-gray-100 last:border-0 last:pb-0">

                            <div class="flex items-center justify-between gap-2">

                                <div class="min-w-0">

                                    <p class="text-sm font-medium text-gray-800 truncate">
                                        {{ $account->name }}
                                    </p>

                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $account->bank_name ?: ucfirst($account->type) }}
                                    </p>

                                </div>

                                <div class="text-right">

                                    <p class="text-sm font-semibold {{ $account->current_balance < 0 ? 'text-red-600' : 'text-gray-800' }}">
                                        {{ $formatCurrency($account->current_balance) }}
                                    </p>

                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="py-8 text-center text-sm text-gray-400">
                            Belum ada rekening aktif.
                        </div>

                    @endforelse

                </div>

            </div>

        </div>


        {{-- ========================================= --}}
        {{-- TRANSAKSI TERAKHIR --}}
        {{-- ========================================= --}}

        <div class="bg-white rounded-xl shadow-sm border border-gray-200">

            <div class="p-5 border-b border-gray-100 flex items-center justify-between">

                <div>
                    <h3 class="font-semibold text-gray-800">
                        Transaksi Terakhir
                    </h3>

                    <p class="text-sm text-gray-400 mt-1">
                        Lima transaksi terbaru
                    </p>
                </div>

                <a href="{{ route('transactions.income') }}"
                   class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                    Lihat Pemasukan
                </a>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full text-sm text-left">

                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">

                        <tr>
                            <th class="px-5 py-4">Tanggal</th>
                            <th class="px-5 py-4">Deskripsi</th>
                            <th class="px-5 py-4">Kategori</th>
                            <th class="px-5 py-4">Rekening</th>
                            <th class="px-5 py-4">Jenis</th>
                            <th class="px-5 py-4 text-right">Nominal</th>
                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse($latestTransactions as $transaction)

                            <tr class="hover:bg-gray-50">

                                {{-- TANGGAL --}}

                                <td class="px-5 py-4 text-gray-500 whitespace-nowrap">
                                    {{ $transaction->transaction_date->format('d/m/Y') }}
                                </td>


                                {{-- DESKRIPSI --}}

                                <td class="px-5 py-4">

                                    <div class="font-medium text-gray-800">
                                        {{ $transaction->description ?: 'Tanpa deskripsi' }}
                                    </div>

                                    @if($transaction->notes)
                                        <div class="text-xs text-gray-400 mt-1">
                                            {{ $transaction->notes }}
                                        </div>
                                    @endif

                                </td>


                                {{-- KATEGORI --}}

                                <td class="px-5 py-4 text-gray-600">

                                    @if($transaction->category)

                                        {{ $transaction->category->icon ?? '' }}
                                        {{ $transaction->category->name }}

                                    @elseif($transaction->type === 'transfer')

                                        Transfer

                                    @else

                                        -

                                    @endif

                                </td>


                                {{-- REKENING --}}

                                <td class="px-5 py-4 text-gray-600">

                                    @if($transaction->type === 'transfer')

                                        <div>
                                            {{ $transaction->account?->name ?? '-' }}
                                        </div>

                                        <div class="text-xs text-gray-400 mt-1">
                                            Ke: {{ $transaction->destinationAccount?->name ?? '-' }}
                                        </div>

                                    @else

                                        {{ $transaction->account?->name ?? '-' }}

                                    @endif

                                </td>


                                {{-- JENIS --}}

                                <td class="px-5 py-4">

                                    @if($transaction->type === 'income')

                                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-medium">
                                            Pemasukan
                                        </span>

                                    @elseif($transaction->type === 'expense')

                                        <span class="px-2.5 py-1 bg-red-50 text-red-600 rounded-full text-xs font-medium">
                                            Pengeluaran
                                        </span>

                                    @else

                                        <span class="px-2.5 py-1 bg-indigo-50 text-indigo-600 rounded-full text-xs font-medium">
                                            Transfer
                                        </span>

                                    @endif

                                </td>


                                {{-- NOMINAL --}}

                                <td class="px-5 py-4 text-right font-semibold whitespace-nowrap">

                                    <span class="{{ $transaction->type === 'income' ? 'text-emerald-600' : ($transaction->type === 'expense' ? 'text-red-500' : 'text-indigo-600') }}">

                                        {{ $transaction->type === 'income' ? '+' : ($transaction->type === 'expense' ? '-' : '') }}

                                        {{ $formatCurrency($transaction->amount) }}

                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                                    Belum ada transaksi.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- ========================================= --}}
    {{-- DASHBOARD PROGRESS BAR SCRIPT --}}
    {{-- ========================================= --}}

    <script>
        (function () {

            function initializeDashboardProgressBars() {

                const progressBars = document.querySelectorAll(
                    '.js-dashboard-progress'
                );

                progressBars.forEach(function (bar) {

                    const progress = parseFloat(bar.dataset.progress) || 0;
                    const color = bar.dataset.color || '#22c55e';
                    const minWidth = parseFloat(bar.dataset.minWidth) || 0;

                    const safeProgress = Math.min(
                        Math.max(progress, 0),
                        100
                    );

                    bar.style.width = safeProgress + '%';
                    bar.style.backgroundColor = color;

                    if (safeProgress > 0 && minWidth > 0) {
                        bar.style.minWidth = minWidth + 'px';
                    }

                });

            }

            if (document.readyState === 'loading') {
                document.addEventListener(
                    'DOMContentLoaded',
                    initializeDashboardProgressBars
                );
            } else {
                initializeDashboardProgressBars();
            }

        })();
    </script>

</x-finance-layout>
