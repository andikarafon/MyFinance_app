
<x-finance-layout>
    <x-slot name="title">Anggaran Bulanan</x-slot>

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

        $formatRupiah = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
    @endphp

    <style>
        /* HEADER BUTTON */
        .budget-add-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background-color: #1e293b;
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
            min-height: 44px;
            transition: background-color 0.2s ease;
        }

        .budget-add-button:hover {
            background-color: #334155;
        }

        /* SUMMARY CARDS */
        .budget-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            width: 100%;
        }

        /* BUDGET DETAILS */
        .budget-detail-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            width: 100%;
            margin-top: 16px;
        }

        /* PROGRESS BAR */
        .budget-progress-track {
            width: 100%;
            height: 10px;
            background-color: #e5e7eb;
            border-radius: 999px;
            overflow: hidden;
        }

        .budget-progress-fill {
            display: block;
            width: 0;
            height: 100%;
            border-radius: 999px;
            transition: width 0.4s ease;
        }

        /* RESPONSIVE */
        @media (max-width: 767px) {
            .budget-summary-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .budget-detail-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }
        }
    </style>

    <div class="space-y-6">

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
               class="budget-add-button">

                <span style="font-size: 20px; line-height: 1;">+</span>
                <span>Tambah Anggaran</span>

            </a>

        </div>

        {{-- NOTIFICATION --}}
        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FILTER PERIODE --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

            <form method="GET"
                  action="{{ route('budgets.index') }}"
                  class="flex flex-col gap-3 sm:flex-row sm:items-end">

                <div>
                    <label for="month" class="mb-1 block text-sm font-medium text-gray-700">
                        Bulan
                    </label>

                    <select name="month"
                            id="month"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-48">

                        @foreach ($monthNames as $number => $name)
                            <option value="{{ $number }}"
                                @selected($month == $number)>
                                {{ $name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div>
                    <label for="year" class="mb-1 block text-sm font-medium text-gray-700">
                        Tahun
                    </label>

                    <input type="number"
                           name="year"
                           id="year"
                           value="{{ $year }}"
                           min="2000"
                           max="2100"
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-32">
                </div>

                <button type="submit"
                        class="rounded-lg bg-gray-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-700">
                    Tampilkan
                </button>

            </form>

        </div>

        {{-- SUMMARY CARDS --}}
        <div class="budget-summary-grid">

            {{-- TOTAL ANGGARAN --}}
            <div class="min-w-0 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

                <p class="text-sm font-medium text-gray-500">
                    Total Anggaran
                </p>

                <h2 class="mt-2 text-2xl font-bold text-gray-800">
                    {{ $formatRupiah($totalBudget) }}
                </h2>

                <p class="mt-1 text-xs text-gray-400">
                    Periode {{ $monthNames[$month] }} {{ $year }}
                </p>

            </div>

            {{-- TOTAL REALISASI --}}
            <div class="min-w-0 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

                <p class="text-sm font-medium text-gray-500">
                    Total Realisasi
                </p>

                <h2 class="mt-2 text-2xl font-bold text-red-600">
                    {{ $formatRupiah($totalRealization) }}
                </h2>

                <p class="mt-1 text-xs text-gray-400">
                    Pengeluaran yang sudah tercatat
                </p>

            </div>

            {{-- SISA ANGGARAN --}}
            <div class="min-w-0 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

                <p class="text-sm font-medium text-gray-500">
                    Sisa Anggaran
                </p>

                <h2 class="mt-2 text-2xl font-bold {{ $totalRemaining < 0 ? 'text-red-600' : 'text-green-600' }}">
                    {{ $formatRupiah($totalRemaining) }}
                </h2>

                <p class="mt-1 text-xs text-gray-400">
                    Anggaran dikurangi realisasi
                </p>

            </div>

        </div>

        {{-- BUDGET LIST --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

            {{-- LIST HEADER --}}
            <div class="border-b border-gray-200 px-5 py-4">

                <h2 class="font-semibold text-gray-800">
                    Rincian Anggaran
                </h2>

                <p class="mt-1 text-xs text-gray-500">
                    {{ $monthNames[$month] }} {{ $year }}
                </p>

            </div>

            {{-- BUDGET DATA --}}
            @forelse ($budgets as $budget)

                @php
                    $percentage = (float) $budget->percentage;
                    $progressWidth = min(max($percentage, 0), 100);

                    if ($percentage >= 100) {
                        $statusColor = 'text-red-600 bg-red-50';
                        $statusText = 'Melebihi Anggaran';
                        $progressColor = '#ef4444';
                    } elseif ($percentage >= 80) {
                        $statusColor = 'text-yellow-700 bg-yellow-50';
                        $statusText = 'Mendekati Batas';
                        $progressColor = '#eab308';
                    } else {
                        $statusColor = 'text-green-700 bg-green-50';
                        $statusText = 'Aman';
                        $progressColor = '#22c55e';
                    }
                @endphp

                {{-- BUDGET ITEM --}}
                <div class="border-b border-gray-100 px-5 py-5 last:border-b-0">

                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">

                        {{-- LEFT CONTENT --}}
                        <div class="min-w-0 flex-1">

                            {{-- CATEGORY & STATUS --}}
                            <div class="flex flex-wrap items-center gap-2">

                                <h3 class="font-semibold text-gray-800">
                                    {{ $budget->category->icon ?? '📁' }}
                                    {{ $budget->category->name ?? 'Kategori tidak tersedia' }}
                                </h3>

                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $statusColor }}">
                                    {{ $statusText }}
                                </span>

                            </div>

                            {{-- DETAIL ANGGARAN: 3 KOLOM --}}
                            <div class="budget-detail-grid">

                                {{-- ANGGARAN --}}
                                <div class="min-w-0">

                                    <p class="text-xs text-gray-500">
                                        Anggaran
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-gray-800">
                                        {{ $formatRupiah($budget->amount) }}
                                    </p>

                                </div>

                                {{-- REALISASI --}}
                                <div class="min-w-0">

                                    <p class="text-xs text-gray-500">
                                        Realisasi
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-red-600">
                                        {{ $formatRupiah($budget->realization) }}
                                    </p>

                                </div>

                                {{-- SISA ANGGARAN --}}
                                <div class="min-w-0">

                                    <p class="text-xs text-gray-500">
                                        Sisa Anggaran
                                    </p>

                                    <p class="mt-1 text-sm font-semibold {{ $budget->remaining < 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $formatRupiah($budget->remaining) }}
                                    </p>

                                </div>

                            </div>

                            {{-- PROGRESS BAR --}}
                            <div class="mt-5 w-full">

                                <div class="mb-2 flex items-center justify-between">

                                    <span class="text-xs text-gray-500">
                                        Pemakaian Anggaran
                                    </span>

                                    <span class="text-xs font-semibold text-gray-700">
                                        {{ number_format($percentage, 1, ',', '.') }}%
                                    </span>

                                </div>

                                {{-- PROGRESS TRACK --}}
                                <div class="budget-progress-track">

                                    {{-- PROGRESS VALUE --}}
                                    <div
                                        class="budget-progress-fill"
                                        data-progress="{{ $progressWidth }}"
                                        data-color="{{ $progressColor }}"
                                    ></div>

                                </div>

                            </div>

                        </div>

                        {{-- ACTION BUTTONS --}}
                        <div class="flex shrink-0 items-center gap-2 lg:justify-end">

                            {{-- EDIT --}}
                            <a href="{{ route('budgets.edit', $budget->id) }}"
                               class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                Edit
                            </a>

                            {{-- DELETE --}}
                            <form method="POST"
                                  action="{{ route('budgets.destroy', $budget->id) }}"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus anggaran ini?')">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="rounded-lg border border-red-200 px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50">
                                    Hapus
                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            @empty

                {{-- EMPTY STATE --}}
                <div class="px-5 py-14 text-center">

                    <div class="text-4xl">
                        📊
                    </div>

                    <h3 class="mt-3 font-semibold text-gray-800">
                        Belum Ada Anggaran
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Belum ada anggaran untuk {{ $monthNames[$month] }} {{ $year }}.
                    </p>

                    <a href="{{ route('budgets.create', ['month' => $month, 'year' => $year]) }}"
                       class="budget-add-button mt-4">

                        <span style="font-size: 20px; line-height: 1;">+</span>
                        <span>Tambah Anggaran</span>

                    </a>

                </div>

            @endforelse

        </div>

    </div>

    {{-- PROGRESS BAR JAVASCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const progressBars = document.querySelectorAll('.budget-progress-fill');

            progressBars.forEach(function (bar) {

                const progress = parseFloat(bar.dataset.progress) || 0;
                const color = bar.dataset.color || '#22c55e';

                const safeProgress = Math.min(Math.max(progress, 0), 100);

                bar.style.width = safeProgress + '%';
                bar.style.backgroundColor = color;

            });

        });
    </script>

</x-finance-layout>
