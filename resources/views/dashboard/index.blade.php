<x-finance-layout>
    <x-slot name="title">
        Dashboard
    </x-slot>

    @php
        $formatCurrency = fn ($amount) =>
            'Rp ' . number_format((float) $amount, 0, ',', '.');

        $currentMonth = now()->translatedFormat('F Y');
    @endphp

    <div class="space-y-6">

        {{-- SUMMARY CARDS --}}
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


            {{-- PEMASUKAN --}}
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


            {{-- PENGELUARAN --}}
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


        {{-- GRAFIK DAN SALDO REKENING --}}
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

                                    <div style="width: {{ number_format($incomeHeight, 2, '.', '') }}%; height: 16px; background-color: #10b981; border-radius: 999px; min-width: {{ $item['income'] > 0 ? '4px' : '0' }};">
                                    </div>

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

                                    <div style="width: {{ number_format($expenseHeight, 2, '.', '') }}%; height: 16px; background-color: #f87171; border-radius: 999px; min-width: {{ $item['expense'] > 0 ? '4px' : '0' }};">
                                    </div>

                                </div>

                                <div class="w-28 text-right text-xs font-medium text-gray-600 shrink-0">
                                    {{ $formatCurrency($item['expense']) }}
                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>


                @if($chartData->sum('income') == 0 && $chartData->sum('expense') == 0)
                    <div class="text-center text-xs text-gray-400 mt-6">
                        Belum ada data pemasukan atau pengeluaran.
                    </div>
                @endif



                @if($chartData->sum('income') == 0 && $chartData->sum('expense') == 0)

                    <div class="text-center text-xs text-gray-400 mt-6">
                        Belum ada data pemasukan atau pengeluaran.
                    </div>

                @endif

            </div>


            {{-- SALDO REKENING --}}
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


        {{-- TRANSAKSI TERAKHIR --}}
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


</x-finance-layout>
