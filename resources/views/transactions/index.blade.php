<x-finance-layout :title="match($type) {
    'income' => 'Pemasukan',
    'expense' => 'Pengeluaran',
    'transfer' => 'Transfer Rekening',
    default => 'Transaksi'
}">

@php
    $isIncome = $type === 'income';
    $isExpense = $type === 'expense';
    $isTransfer = $type === 'transfer';

    $pageTitle = match($type) {
        'income' => 'Pemasukan',
        'expense' => 'Pengeluaran',
        'transfer' => 'Transfer Rekening',
        default => 'Transaksi'
    };

    $pageDescription = match($type) {
        'income' => 'Kelola seluruh transaksi pemasukan Anda.',
        'expense' => 'Kelola seluruh transaksi pengeluaran Anda.',
        'transfer' => 'Kelola pemindahan saldo antar rekening.',
        default => 'Kelola transaksi keuangan Anda.'
    };

    $createLabel = match($type) {
        'income' => 'Tambah Pemasukan',
        'expense' => 'Tambah Pengeluaran',
        'transfer' => 'Transfer Rekening',
        default => 'Tambah Transaksi'
    };

    $indexRoute = match($type) {
        'income' => 'transactions.income',
        'expense' => 'transactions.expense',
        'transfer' => 'transactions.transfer',
        default => 'dashboard'
    };

    $totalTransactions = $transactions->total();

    $formatCurrency = fn ($amount) => 'Rp ' . number_format((float) $amount, 0, ',', '.');
@endphp

<div class="space-y-6">


        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ $pageTitle }}
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    {{ $pageDescription }}
                </p>
            </div>

            <a href="{{ route('transactions.create', ['type' => $type]) }}"
            style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 11px 20px; background-color: #4f46e5; color: #ffffff; border-radius: 8px; font-weight: 600; text-decoration: none; white-space: nowrap;">

                <span style="font-size: 20px; line-height: 1;">+</span>
                <span>Tambah Transaksi</span>

            </a>

        </div>



    {{-- SUMMARY CARDS --}}
    <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px;">

        {{-- TOTAL TRANSAKSI --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Transaksi</p>

                    <h2 class="text-2xl font-bold text-gray-800 mt-2">
                        {{ number_format($totalTransactions, 0, ',', '.') }}
                    </h2>
                </div>

                <div class="w-11 h-11 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 text-xl">
                    ⇄
                </div>
            </div>

            <p class="text-xs text-gray-400 mt-3">
                Seluruh data transaksi
            </p>

        </div>


        {{-- TOTAL REKENING --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Rekening</p>

                    <h2 class="text-2xl font-bold text-gray-800 mt-2">
                        {{ $accounts->count() }}
                    </h2>
                </div>

                <div class="w-11 h-11 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 text-xl">
                    ▣
                </div>
            </div>

            <p class="text-xs text-gray-400 mt-3">
                Rekening yang tersedia
            </p>

        </div>


        {{-- TOTAL KATEGORI --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Kategori</p>

                    <h2 class="text-2xl font-bold text-gray-800 mt-2">
                        {{ $categories->count() }}
                    </h2>
                </div>

                <div class="w-11 h-11 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 text-xl">
                    ▤
                </div>
            </div>

            <p class="text-xs text-gray-400 mt-3">
                Kategori yang tersedia
            </p>

        </div>

    </div>


    {{-- FILTER TRANSAKSI --}}
    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">

        <form action="{{ url()->current() }}" method="GET">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                {{-- SEARCH --}}
                <div class="md:col-span-2">

                    <label for="search"
                           class="block text-sm font-medium text-gray-700 mb-2">
                        Pencarian
                    </label>

                    <input type="text"
                           name="search"
                           id="search"
                           value="{{ request('search') }}"
                           placeholder="Cari deskripsi atau catatan..."
                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                </div>


                {{-- START DATE --}}
                <div>

                    <label for="start_date"
                           class="block text-sm font-medium text-gray-700 mb-2">
                        Dari Tanggal
                    </label>

                    <input type="date"
                           name="start_date"
                           id="start_date"
                           value="{{ request('start_date') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                </div>


                {{-- END DATE --}}
                <div>

                    <label for="end_date"
                           class="block text-sm font-medium text-gray-700 mb-2">
                        Sampai Tanggal
                    </label>

                    <input type="date"
                           name="end_date"
                           id="end_date"
                           value="{{ request('end_date') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                </div>

            </div>


            <div class="flex flex-wrap items-center justify-end gap-3 mt-5">

                <a href="{{ route($indexRoute) }}"
                   class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition">
                    Reset
                </a>

                <button type="submit"
                        class="px-5 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-900 transition">
                    Cari Transaksi
                </button>

            </div>

        </form>

    </div>


    {{-- TRANSACTION TABLE --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-5 py-4 border-b border-gray-100">

            <div>
                <h2 class="font-semibold text-gray-800">
                    Daftar {{ $pageTitle }}
                </h2>

                <p class="text-xs text-gray-500 mt-1">
                    Menampilkan {{ $transactions->count() }} dari {{ $totalTransactions }} transaksi
                </p>
            </div>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-sm text-left">

                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">

                    <tr>

                        <th class="px-5 py-4 font-semibold whitespace-nowrap">
                            Tanggal
                        </th>

                        <th class="px-5 py-4 font-semibold">
                            Deskripsi
                        </th>

                        @if ($isTransfer)

                            <th class="px-5 py-4 font-semibold">
                                Rekening Asal
                            </th>

                            <th class="px-5 py-4 font-semibold">
                                Rekening Tujuan
                            </th>

                        @else

                            <th class="px-5 py-4 font-semibold">
                                Kategori
                            </th>

                            <th class="px-5 py-4 font-semibold">
                                Rekening
                            </th>

                        @endif

                        <th class="px-5 py-4 font-semibold text-right whitespace-nowrap">
                            Nominal
                        </th>

                        <th class="px-5 py-4 font-semibold text-center">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-100">

                    @forelse ($transactions as $transaction)

                        <tr class="hover:bg-gray-50 transition">

                            {{-- TANGGAL --}}
                            <td class="px-5 py-4 whitespace-nowrap text-gray-600">

                                {{ $transaction->transaction_date
                                    ? $transaction->transaction_date->format('d/m/Y')
                                    : '-' }}

                            </td>


                            {{-- DESKRIPSI --}}
                            <td class="px-5 py-4">

                                <div class="font-medium text-gray-800">
                                    {{ $transaction->description ?: 'Tanpa deskripsi' }}
                                </div>

                                @if ($transaction->notes)
                                    <div class="text-xs text-gray-400 mt-1 max-w-xs truncate">
                                        {{ $transaction->notes }}
                                    </div>
                                @endif

                            </td>


                            {{-- KATEGORI / REKENING ASAL --}}
                            @if ($isTransfer)

                                <td class="px-5 py-4 text-gray-600 whitespace-nowrap">
                                    {{ $transaction->account?->name ?? '-' }}
                                </td>

                                <td class="px-5 py-4 text-gray-600 whitespace-nowrap">
                                    {{ $transaction->destinationAccount?->name ?? '-' }}
                                </td>

                            @else

                                <td class="px-5 py-4 whitespace-nowrap">

                                    @if ($transaction->category)
                                        <span class="inline-flex items-center gap-2 text-gray-700">
                                            <span>
                                                {{ $transaction->category->icon ?? '📁' }}
                                            </span>

                                            <span>
                                                {{ $transaction->category->name }}
                                            </span>
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif

                                </td>

                                <td class="px-5 py-4 text-gray-600 whitespace-nowrap">
                                    {{ $transaction->account?->name ?? '-' }}
                                </td>

                            @endif


                            {{-- NOMINAL --}}
                            <td class="px-5 py-4 text-right whitespace-nowrap">

                                <span class="font-semibold
                                    {{ $isIncome ? 'text-green-600' : ($isExpense ? 'text-red-600' : 'text-indigo-600') }}">

                                    {{ $isIncome ? '+' : ($isExpense ? '-' : '') }}
                                    {{ $formatCurrency($transaction->amount) }}

                                </span>

                            </td>


                            {{-- AKSI --}}
                            <td class="px-5 py-4">

                                <div class="flex items-center justify-center gap-2">

                                    {{-- EDIT --}}
                                    <a href="{{ route('transactions.edit', [
                                        'type' => $type,
                                        'transaction' => $transaction->id
                                    ]) }}"
                                       title="Edit transaksi"
                                       class="w-9 h-9 flex items-center justify-center rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition">

                                        ✎

                                    </a>


                                    {{-- DELETE --}}
                                    <form action="{{ route('transactions.destroy', [
                                        'type' => $type,
                                        'transaction' => $transaction->id
                                    ]) }}"
                                          method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                title="Hapus transaksi"
                                                class="w-9 h-9 flex items-center justify-center rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition">

                                            🗑

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6" class="px-5 py-16 text-center">

                                <div class="flex flex-col items-center justify-center">

                                    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center text-3xl mb-4">
                                        📋
                                    </div>

                                    <h3 class="font-semibold text-gray-700">
                                        Belum Ada Transaksi
                                    </h3>

                                    <p class="text-sm text-gray-400 mt-2">
                                        Belum ada data {{ strtolower($pageTitle) }} yang tersedia.
                                    </p>

                                    <a href="{{ route('transactions.create', ['type' => $type]) }}"
                                       class="inline-flex items-center gap-2 mt-5 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition">

                                        <span>+</span>
                                        <span>{{ $createLabel }}</span>

                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if ($transactions->hasPages())

            <div class="px-5 py-4 border-t border-gray-100">

                {{ $transactions->withQueryString()->links() }}

            </div>

        @endif

    </div>

</div>

</x-finance-layout>
