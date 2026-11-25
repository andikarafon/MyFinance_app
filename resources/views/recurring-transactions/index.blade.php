<x-finance-layout title="Transaksi Berulang">

    <div class="space-y-6">

        {{-- HEADER HALAMAN --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-xl font-bold text-slate-800">
                    Transaksi Berulang
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Kelola jadwal pemasukan dan pengeluaran otomatis.
                </p>
            </div>

            <a href="{{ route('recurring-transactions.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">

                <span class="text-lg leading-none">+</span>
                Tambah Jadwal

            </a>

        </div>


        {{-- FLASH MESSAGE --}}
        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif


        {{-- FILTER --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

            <form method="GET"
                  action="{{ route('recurring-transactions.index') }}"
                  class="grid grid-cols-1 gap-4 md:grid-cols-4">

                {{-- PENCARIAN --}}
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">
                        Pencarian
                    </label>

                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari nama jadwal..."
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                </div>


                {{-- FILTER JENIS --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">
                        Jenis Transaksi
                    </label>

                    <select name="type"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">

                        <option value="">Semua Jenis</option>

                        <option value="income"
                            {{ request('type') === 'income' ? 'selected' : '' }}>
                            Pemasukan
                        </option>

                        <option value="expense"
                            {{ request('type') === 'expense' ? 'selected' : '' }}>
                            Pengeluaran
                        </option>

                    </select>
                </div>


                {{-- FILTER STATUS --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">
                        Status
                    </label>

                    <select name="status"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">

                        <option value="">Semua Status</option>

                        <option value="active"
                            {{ request('status') === 'active' ? 'selected' : '' }}>
                            Aktif
                        </option>

                        <option value="inactive"
                            {{ request('status') === 'inactive' ? 'selected' : '' }}>
                            Nonaktif
                        </option>

                        <option value="completed"
                            {{ request('status') === 'completed' ? 'selected' : '' }}>
                            Selesai
                        </option>

                    </select>
                </div>



                {{-- TOMBOL FILTER --}}
                <div class="flex flex-wrap items-center gap-2 md:col-span-4">

                    <button
                        type="submit"
                        style="display: inline-flex; align-items: center; justify-content: center; background-color: #059669; color: #ffffff; min-height: 40px;"
                        class="rounded-lg px-4 py-2 text-sm font-semibold shadow-sm hover:bg-emerald-700 transition"
                    >
                        Tampilkan
                    </button>

                    <a
                        href="{{ route('recurring-transactions.index') }}"
                        style="display: inline-flex; align-items: center; justify-content: center; min-height: 40px;"
                        class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
                    >
                        Reset
                    </a>

                </div>


            </form>

        </div>


        {{-- TABEL TRANSAKSI BERULANG --}}
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

            {{-- HEADER TABEL --}}
            <div class="flex flex-col gap-2 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h3 class="font-semibold text-slate-800">
                        Daftar Jadwal
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Daftar seluruh jadwal transaksi berulang Anda.
                    </p>
                </div>

                <span class="text-xs font-medium text-slate-500">
                    Total: {{ $recurringTransactions->total() }} jadwal
                </span>

            </div>


            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-slate-100">

                    <thead class="bg-slate-50">

                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Nama Jadwal
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Jenis
                            </th>

                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Nominal
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Frekuensi
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Jadwal Berikutnya
                            </th>

                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Status
                            </th>

                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Aksi
                            </th>
                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse ($recurringTransactions as $recurringTransaction)

                            @php
                                $frequencyLabels = [
                                    'daily' => 'Hari',
                                    'weekly' => 'Minggu',
                                    'monthly' => 'Bulan',
                                    'yearly' => 'Tahun',
                                ];

                                $frequencyLabel = $frequencyLabels[$recurringTransaction->frequency] ?? $recurringTransaction->frequency;

                                $statusClasses = [
                                    'active' => 'bg-emerald-50 text-emerald-700',
                                    'inactive' => 'bg-slate-100 text-slate-600',
                                    'completed' => 'bg-blue-50 text-blue-700',
                                ];

                                $statusLabels = [
                                    'active' => 'Aktif',
                                    'inactive' => 'Nonaktif',
                                    'completed' => 'Selesai',
                                ];
                            @endphp

                            <tr class="transition hover:bg-slate-50">

                                {{-- NAMA JADWAL --}}
                                <td class="px-5 py-4">

                                    <a href="{{ route('recurring-transactions.show', $recurringTransaction) }}"
                                       class="font-semibold text-slate-800 hover:text-emerald-600">

                                        {{ $recurringTransaction->name }}

                                    </a>

                                    <p class="mt-1 text-xs text-slate-400">
                                        {{ $recurringTransaction->account?->name ?? '-' }}
                                    </p>

                                </td>


                                {{-- JENIS --}}
                                <td class="px-5 py-4">

                                    @if ($recurringTransaction->type === 'income')

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
                                <td class="whitespace-nowrap px-5 py-4 text-right font-semibold">

                                    <span class="{{ $recurringTransaction->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                                        Rp {{ number_format((float) $recurringTransaction->amount, 0, ',', '.') }}
                                    </span>

                                </td>


                                {{-- FREKUENSI --}}
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">

                                    Setiap
                                    {{ $recurringTransaction->interval }}
                                    {{ $frequencyLabel }}

                                </td>


                                {{-- JADWAL BERIKUTNYA --}}
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">

                                    {{ $recurringTransaction->next_run_date?->format('d/m/Y') ?? '-' }}

                                </td>


                                {{-- STATUS --}}
                                <td class="px-5 py-4 text-center">

                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses[$recurringTransaction->status] ?? 'bg-slate-100 text-slate-600' }}">

                                        {{ $statusLabels[$recurringTransaction->status] ?? ucfirst($recurringTransaction->status) }}

                                    </span>

                                </td>


                                {{-- AKSI --}}
                                <td class="px-5 py-4">

                                    <div class="flex items-center justify-center gap-2">

                                        {{-- DETAIL --}}
                                        <a href="{{ route('recurring-transactions.show', $recurringTransaction) }}"
                                           title="Detail"
                                           class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-100">
                                            Detail
                                        </a>


                                        {{-- EDIT --}}
                                        @if ($recurringTransaction->status !== 'completed')

                                            <a href="{{ route('recurring-transactions.edit', $recurringTransaction) }}"
                                               title="Edit"
                                               class="rounded-lg border border-blue-200 px-2.5 py-1.5 text-xs font-medium text-blue-600 transition hover:bg-blue-50">
                                                Edit
                                            </a>

                                        @endif


                                        {{-- AKTIFKAN --}}
                                        @if ($recurringTransaction->status === 'inactive')

                                            <form method="POST"
                                                  action="{{ route('recurring-transactions.activate', $recurringTransaction) }}">

                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                        title="Aktifkan"
                                                        class="rounded-lg border border-emerald-200 px-2.5 py-1.5 text-xs font-medium text-emerald-600 transition hover:bg-emerald-50">
                                                    Aktifkan
                                                </button>

                                            </form>

                                        @endif


                                        {{-- NONAKTIFKAN --}}
                                        @if ($recurringTransaction->status === 'active')

                                            <form method="POST"
                                                  action="{{ route('recurring-transactions.deactivate', $recurringTransaction) }}">

                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                        title="Nonaktifkan"
                                                        onclick="return confirm('Nonaktifkan jadwal transaksi ini?')"
                                                        class="rounded-lg border border-amber-200 px-2.5 py-1.5 text-xs font-medium text-amber-600 transition hover:bg-amber-50">
                                                    Nonaktifkan
                                                </button>

                                            </form>

                                        @endif


                                        {{-- HAPUS --}}
                                        <form method="POST"
                                              action="{{ route('recurring-transactions.destroy', $recurringTransaction) }}">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    title="Hapus"
                                                    onclick="return confirm('Hapus jadwal ini? Riwayat transaksi akan tetap disimpan.')"
                                                    class="rounded-lg border border-red-200 px-2.5 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50">
                                                Hapus
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center">

                                    <div class="text-sm font-medium text-slate-500">
                                        Belum ada jadwal transaksi berulang.
                                    </div>

                                    <p class="mt-1 text-xs text-slate-400">
                                        Tambahkan jadwal untuk mengatur pemasukan atau pengeluaran secara berkala.
                                    </p>

                                    <a href="{{ route('recurring-transactions.create') }}"
                                       class="mt-4 inline-flex rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                                        Tambah Jadwal
                                    </a>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- PAGINATION --}}
            @if ($recurringTransactions->hasPages())

                <div class="border-t border-slate-100 px-5 py-4">

                    {{ $recurringTransactions->links() }}

                </div>

            @endif

        </div>

    </div>

</x-finance-layout>
