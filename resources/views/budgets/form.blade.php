<x-finance-layout>
    <x-slot name="title">
        {{ $isEdit ? 'Edit Anggaran' : 'Tambah Anggaran' }}
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
    @endphp

    <div class="max-w-3xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Anggaran Bulanan
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Kelola batas pengeluaran berdasarkan kategori setiap bulan.
            </p>
        </div>

        <a href="{{ route('budgets.create', ['month' => $month, 'year' => $year]) }}"
        class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-800 px-5 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 whitespace-nowrap">

            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 4v16m8-8H4" />
            </svg>

            Tambah Anggaran
        </a>

    </div>

        {{-- VALIDATION ERRORS --}}
        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                <div class="font-semibold text-red-700 mb-2">
                    Terdapat kesalahan input:
                </div>

                <ul class="list-disc list-inside space-y-1 text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">

            <div class="p-6 border-b border-slate-200">
                <h2 class="font-semibold text-slate-800">
                    Informasi Anggaran
                </h2>

                <p class="text-xs text-slate-500 mt-1">
                    Lengkapi seluruh informasi berikut.
                </p>
            </div>

            <form
                method="POST"
                action="{{ $isEdit
                    ? route('budgets.update', $budget->id)
                    : route('budgets.store') }}"
                class="p-6 space-y-5"
            >

                @csrf

                @if ($isEdit)
                    @method('PUT')
                @endif

                {{-- KATEGORI --}}
                <div>
                    <label for="category_id"
                           class="block text-sm font-medium text-slate-700 mb-2">
                        Kategori Pengeluaran
                        <span class="text-red-500">*</span>
                    </label>

                    <select
                        name="category_id"
                        id="category_id"
                        required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none"
                    >
                        <option value="">Pilih Kategori</option>

                        @foreach ($categories as $category)
                            <option
                                value="{{ $category->id }}"
                                @selected(old('category_id', $budget->category_id) == $category->id)
                            >
                                {{ $category->icon ?? '📁' }}
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('category_id')
                        <p class="text-xs text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                    @if ($categories->isEmpty())
                        <p class="text-xs text-amber-600 mt-2">
                            Belum ada kategori pengeluaran aktif.
                            Silakan tambahkan kategori terlebih dahulu di menu Kategori.
                        </p>
                    @endif
                </div>

                {{-- PERIODE --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- BULAN --}}
                    <div>
                        <label for="month"
                               class="block text-sm font-medium text-slate-700 mb-2">
                            Bulan
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            name="month"
                            id="month"
                            required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none"
                        >
                            @foreach ($monthNames as $number => $name)
                                <option
                                    value="{{ $number }}"
                                    @selected(old('month', $month) == $number)
                                >
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>

                        @error('month')
                            <p class="text-xs text-red-500 mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- TAHUN --}}
                    <div>
                        <label for="year"
                               class="block text-sm font-medium text-slate-700 mb-2">
                            Tahun
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="number"
                            name="year"
                            id="year"
                            value="{{ old('year', $year) }}"
                            min="2000"
                            max="2100"
                            required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none"
                        >

                        @error('year')
                            <p class="text-xs text-red-500 mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

                {{-- NOMINAL ANGGARAN --}}
                <div>
                    <label for="amount"
                           class="block text-sm font-medium text-slate-700 mb-2">
                        Nominal Anggaran
                        <span class="text-red-500">*</span>
                    </label>

                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-medium text-slate-500">
                            Rp
                        </span>

                        <input
                            type="number"
                            name="amount"
                            id="amount"
                            value="{{ old('amount', $budget->amount) }}"
                            min="1"
                            step="0.01"
                            placeholder="Contoh: 1500000"
                            required
                            class="w-full rounded-lg border border-slate-300 pl-10 pr-3 py-3 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none"
                        >
                    </div>

                    <p class="text-xs text-slate-400 mt-2">
                        Masukkan batas maksimal pengeluaran untuk kategori ini.
                    </p>

                    @error('amount')
                        <p class="text-xs text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- INFORMATION --}}
                <div class="rounded-lg bg-blue-50 border border-blue-100 p-4">
                    <div class="flex gap-3">
                        <div class="text-blue-600 text-lg">
                            ℹ
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-blue-800">
                                Informasi Anggaran
                            </p>

                            <p class="text-xs text-blue-700 mt-1 leading-relaxed">
                                Realisasi anggaran akan dihitung otomatis
                                berdasarkan transaksi pengeluaran pada kategori
                                dan periode yang dipilih. Transaksi transfer
                                antar rekening tidak dihitung sebagai pengeluaran.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4 border-t border-slate-100">

                    {{-- BATAL --}}
                    <a href="{{ route('budgets.index', ['month' => $month, 'year' => $year]) }}"
                    style="
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            padding: 11px 24px;
                            border: 1px solid #cbd5e1;
                            border-radius: 8px;
                            background-color: #ffffff;
                            color: #475569;
                            font-size: 14px;
                            font-weight: 600;
                            text-decoration: none;
                    ">
                        Batal
                    </a>

                    {{-- SIMPAN / UPDATE --}}
                    <button
                        type="submit"
                        @disabled($categories->isEmpty())
                        style="
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            gap: 8px;
                            padding: 11px 24px;
                            border: none;
                            border-radius: 8px;
                            background-color: #1e293b;
                            color: #ffffff;
                            font-size: 14px;
                            font-weight: 600;
                            cursor: pointer;
                            min-width: 160px;
                            opacity: {{ $categories->isEmpty() ? '0.5' : '1' }};
                        ">

                        <span>{{ $isEdit ? 'Update Anggaran' : 'Simpan Anggaran' }}</span>

                    </button>

                </div>

            </form>

        </div>

    </div>

</x-finance-layout>
