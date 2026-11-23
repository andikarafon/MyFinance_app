<x-finance-layout :title="'Tambah ' . match($type) {
    'income' => 'Pemasukan',
    'expense' => 'Pengeluaran',
    'transfer' => 'Transfer Rekening',
    default => 'Transaksi'
}">

@php
    $isTransfer = $type === 'transfer';
    $isIncome = $type === 'income';

    $pageTitle = match($type) {
        'income' => 'Tambah Pemasukan',
        'expense' => 'Tambah Pengeluaran',
        'transfer' => 'Transfer Rekening',
        default => 'Tambah Transaksi'
    };

    $pageDescription = match($type) {
        'income' => 'Catat uang yang masuk ke rekening Anda.',
        'expense' => 'Catat pengeluaran dan penggunaan uang Anda.',
        'transfer' => 'Pindahkan saldo dari satu rekening ke rekening lainnya.',
        default => 'Catat transaksi keuangan Anda.'
    };
@endphp

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                {{ $pageTitle }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                {{ $pageDescription }}
            </p>
        </div>

        <a href="{{ route(match($type) {
            'income' => 'transactions.income',
            'expense' => 'transactions.expense',
            'transfer' => 'transactions.transfer',
            default => 'dashboard'
        }) }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition">
            <span>←</span>
            Kembali
        </a>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <form action="{{ route('transactions.store', ['type' => $type]) }}"
              method="POST"
              class="p-6 space-y-6">

            @csrf

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="p-4 rounded-lg bg-red-50 border border-red-200">
                    <p class="font-semibold text-red-700 mb-2">
                        Terdapat kesalahan pada input:
                    </p>
                    <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Tanggal Transaksi --}}
            <div>
                <label for="transaction_date"
                       class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Transaksi <span class="text-red-500">*</span>
                </label>

                <input type="date"
                       id="transaction_date"
                       name="transaction_date"
                       value="{{ old('transaction_date', now()->format('Y-m-d')) }}"
                       required
                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                @error('transaction_date')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Rekening --}}
            @if ($isTransfer)

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div>
                        <label for="account_id"
                               class="block text-sm font-medium text-gray-700 mb-2">
                            Rekening Asal <span class="text-red-500">*</span>
                        </label>

                        <select name="account_id"
                                id="account_id"
                                required
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                            <option value="">Pilih rekening asal</option>

                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}"
                                    @selected(old('account_id') == $account->id)>
                                    {{ $account->name }}
                                </option>
                            @endforeach

                        </select>

                        @error('account_id')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="destination_account_id"
                               class="block text-sm font-medium text-gray-700 mb-2">
                            Rekening Tujuan <span class="text-red-500">*</span>
                        </label>

                        <select name="destination_account_id"
                                id="destination_account_id"
                                required
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                            <option value="">Pilih rekening tujuan</option>

                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}"
                                    @selected(old('destination_account_id') == $account->id)>
                                    {{ $account->name }}
                                </option>
                            @endforeach

                        </select>

                        @error('destination_account_id')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <div class="p-4 rounded-lg bg-blue-50 border border-blue-100">
                    <p class="text-sm text-blue-700">
                        Transfer akan mengurangi saldo rekening asal dan menambah saldo rekening tujuan.
                    </p>
                </div>

            @else

                <div>
                    <label for="account_id"
                           class="block text-sm font-medium text-gray-700 mb-2">
                        Rekening <span class="text-red-500">*</span>
                    </label>

                    <select name="account_id"
                            id="account_id"
                            required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                        <option value="">Pilih rekening</option>

                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}"
                                @selected(old('account_id') == $account->id)>
                                {{ $account->name }}
                            </option>
                        @endforeach

                    </select>

                    @error('account_id')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kategori --}}
                <div>
                    <label for="category_id"
                           class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>

                    <select name="category_id"
                            id="category_id"
                            required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                        <option value="">Pilih kategori</option>

                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                @selected(old('category_id') == $category->id)>
                                {{ $category->icon ?? '' }} {{ $category->name }}
                            </option>
                        @endforeach

                    </select>

                    @error('category_id')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

            @endif

            {{-- Nominal --}}
            <div>
                <label for="amount"
                       class="block text-sm font-medium text-gray-700 mb-2">
                    Nominal (Rp) <span class="text-red-500">*</span>
                </label>

                <input type="number"
                       id="amount"
                       name="amount"
                       value="{{ old('amount') }}"
                       min="0.01"
                       step="0.01"
                       placeholder="Contoh: 500000"
                       required
                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                @error('amount')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="description"
                       class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi
                </label>

                <input type="text"
                       id="description"
                       name="description"
                       value="{{ old('description') }}"
                       maxlength="255"
                       placeholder="Contoh: Gaji bulanan, belanja kebutuhan..."
                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                @error('description')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Catatan --}}
            <div>
                <label for="notes"
                       class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan
                </label>

                <textarea name="notes"
                          id="notes"
                          rows="3"
                          placeholder="Tambahkan catatan jika diperlukan..."
                          class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>

                @error('notes')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>


            {{-- Actions --}}
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-5 border-t border-gray-100">

                <a href="{{ route(match($type) {
                    'income' => 'transactions.income',
                    'expense' => 'transactions.expense',
                    'transfer' => 'transactions.transfer',
                    default => 'dashboard'
                }) }}"
                class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-600 text-center hover:bg-gray-50 transition">
                    Batal
                </a>


                <button type="submit"
                        style="display: inline-flex; align-items: center; justify-content: center; gap: 10px; padding: 10px 28px; background-color: #4f46e5; color: #ffffff !important; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; min-width: 130px;">

                    <span>💾</span>
                    <span style="color: #ffffff !important;">Simpan</span>

                </button>


            </div>


        </form>
    </div>

</div>

@if ($isTransfer)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sourceAccount = document.getElementById('account_id');
            const destinationAccount = document.getElementById('destination_account_id');

            function validateAccounts() {
                const sameAccount =
                    sourceAccount.value !== '' &&
                    sourceAccount.value === destinationAccount.value;

                destinationAccount.setCustomValidity(
                    sameAccount ? 'Rekening asal dan tujuan tidak boleh sama.' : ''
                );
            }

            sourceAccount.addEventListener('change', validateAccounts);
            destinationAccount.addEventListener('change', validateAccounts);
        });
    </script>
@endif

</x-finance-layout>
