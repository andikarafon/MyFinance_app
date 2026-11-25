<x-finance-layout title="Detail Transaksi Berulang">

    <div class="mx-auto max-w-6xl space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-xl font-bold text-slate-800">
                    Detail Transaksi Berulang
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Informasi jadwal dan riwayat eksekusi transaksi.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">

                <a href="{{ route('recurring-transactions.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                    Kembali
                </a>

                @if ($recurringTransaction->status !== 'completed')
                    <a href="{{ route('recurring-transactions.edit', $recurringTransaction) }}"
                       class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                        Edit Jadwal
                    </a>
                @endif

            </div>

        </div>


        {{-- FLASH MESSAGE --}}
        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif


        {{-- STATUS DAN NAMA JADWAL --}}
        @php
            $statusLabels = [
                'active' => 'Aktif',
                'inactive' => 'Nonaktif',
                'completed' => 'Selesai',
            ];

            $statusClasses = [
                'active' => 'bg-emerald-50 text-emerald-700',
                'inactive' => 'bg-slate-100 text-slate-600',
                'completed' => 'bg-blue-50 text-blue-700',
            ];

            $frequencyLabels = [
                'daily' => 'Harian',
                'weekly' => 'Mingguan',
                'monthly' => 'Bulanan',
                'yearly' => 'Tahunan',
            ];

            $typeLabel = $recurringTransaction->type === 'income'
                ? 'Pemasukan'
                : 'Pengeluaran';

            $frequencyLabel = $frequencyLabels[$recurringTransaction->frequency]
                ?? $recurringTransaction->frequency;
        @endphp


        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                <div>
                    <div class="flex flex-wrap items-center gap-2">

                        <h3 class="text-lg font-bold text-slate-800">
                            {{ $recurringTransaction->name }}
                        </h3>

                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$recurringTransaction->status] ?? 'bg-slate-100 text-slate-600' }}">
                            {{ $statusLabels[$recurringTransaction->status] ?? ucfirst($recurringTransaction->status) }}
                        </span>

                    </div>

                    <p class="mt-2 text-sm text-slate-500">
                        {{ $typeLabel }} · Setiap
                        {{ $recurringTransaction->interval }}
                        {{ strtolower($frequencyLabel) }}
                    </p>
                </div>


                <div class="text-left sm:text-right">

                    <p class="text-xs font-medium text-slate-500">
                        Nominal Dasar
                    </p>

                    <p class="mt-1 text-2xl font-bold {{ $recurringTransaction->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                        Rp {{ number_format((float) $recurringTransaction->amount, 0, ',', '.') }}
                    </p>

                </div>

            </div>


            {{-- AKSI STATUS --}}
            <div class="mt-5 flex flex-wrap gap-2 border-t border-slate-100 pt-5">

                @if ($recurringTransaction->status === 'active')

                    <form method="POST"
                          action="{{ route('recurring-transactions.deactivate', $recurringTransaction) }}">

                        @csrf
                        @method('PATCH')

                        <button type="submit"
                                onclick="return confirm('Nonaktifkan jadwal ini? Transaksi Pending akan dibatalkan.')"
                                class="rounded-lg border border-red-200 bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">
                            Nonaktifkan Jadwal
                        </button>

                    </form>

                @elseif ($recurringTransaction->status === 'inactive')

                    <form method="POST"
                          action="{{ route('recurring-transactions.activate', $recurringTransaction) }}">

                        @csrf
                        @method('PATCH')

                        <button type="submit"
                                onclick="return confirm('Aktifkan kembali jadwal ini?')"
                                class="rounded-lg border border-emerald-200 px-4 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-50">
                            Aktifkan Jadwal
                        </button>

                    </form>

                @endif

            </div>

        </div>


        {{-- INFORMASI JADWAL --}}
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-100 px-6 py-4">
                <h3 class="font-semibold text-slate-800">
                    Informasi Jadwal
                </h3>
            </div>


            <div class="grid grid-cols-1 gap-6 p-6 sm:grid-cols-2 lg:grid-cols-3">

                <div>
                    <p class="text-xs text-slate-500">Jenis Transaksi</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $typeLabel }}
                    </p>
                </div>


                <div>
                    <p class="text-xs text-slate-500">Rekening</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $recurringTransaction->account?->name ?? '-' }}
                    </p>
                </div>


                <div>
                    <p class="text-xs text-slate-500">Kategori</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $recurringTransaction->category?->name ?? '-' }}
                    </p>
                </div>


                <div>
                    <p class="text-xs text-slate-500">Frekuensi</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        Setiap {{ $recurringTransaction->interval }}
                        {{ strtolower($frequencyLabel) }}
                    </p>
                </div>


                <div>
                    <p class="text-xs text-slate-500">Hari / Tanggal Jadwal</p>

                    <p class="mt-1 text-sm font-semibold text-slate-800">

                        @if ($recurringTransaction->frequency === 'weekly')

                            @php
                                $dayNames = [
                                    1 => 'Senin',
                                    2 => 'Selasa',
                                    3 => 'Rabu',
                                    4 => 'Kamis',
                                    5 => 'Jumat',
                                    6 => 'Sabtu',
                                    7 => 'Minggu',
                                ];
                            @endphp

                            {{ $dayNames[$recurringTransaction->day_of_week] ?? '-' }}

                        @elseif (in_array($recurringTransaction->frequency, ['monthly', 'yearly']))

                            Tanggal {{ $recurringTransaction->day_of_month }}

                            @if ($recurringTransaction->frequency === 'yearly')
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

                                {{ $monthNames[$recurringTransaction->month_of_year] ?? '' }}
                            @endif

                        @else
                            Setiap hari
                        @endif

                    </p>
                </div>


                <div>
                    <p class="text-xs text-slate-500">Tanggal Mulai</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $recurringTransaction->start_date?->format('d/m/Y') ?? '-' }}
                    </p>
                </div>


                <div>
                    <p class="text-xs text-slate-500">Tanggal Berakhir</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $recurringTransaction->end_date?->format('d/m/Y') ?? 'Tidak dibatasi' }}
                    </p>
                </div>


                <div>
                    <p class="text-xs text-slate-500">Jadwal Berikutnya</p>
                    <p class="mt-1 text-sm font-semibold text-emerald-700">
                        {{ $recurringTransaction->next_run_date?->format('d/m/Y') ?? '-' }}
                    </p>
                </div>


                <div>
                    <p class="text-xs text-slate-500">Mode Eksekusi</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $recurringTransaction->confirmation_required ? 'Perlu Konfirmasi' : 'Otomatis' }}
                    </p>
                </div>


                <div>
                    <p class="text-xs text-slate-500">Pengingat</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $recurringTransaction->reminder_days }} hari sebelumnya
                    </p>
                </div>


                <div>
                    <p class="text-xs text-slate-500">Dibuat Pada</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $recurringTransaction->created_at?->format('d/m/Y H:i') ?? '-' }}
                    </p>
                </div>

            </div>

        </div>


        {{-- RIWAYAT TRANSAKSI --}}
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

            <div class="flex flex-col gap-2 border-b border-slate-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h3 class="font-semibold text-slate-800">
                        Riwayat Transaksi
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Daftar seluruh proses jadwal transaksi ini.
                    </p>
                </div>

                <span class="text-xs font-medium text-slate-500">
                    Total: {{ $logs->total() }} riwayat
                </span>

            </div>


            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-slate-100">

                    <thead class="bg-slate-50">

                        <tr>
                            <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Tanggal Jadwal
                            </th>

                            <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Tanggal Transaksi
                            </th>

                            <th class="whitespace-nowrap px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Nominal
                            </th>

                            <th class="whitespace-nowrap px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Status
                            </th>

                            <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Keterangan
                            </th>

                            <th class="whitespace-nowrap px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Aksi
                            </th>
                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse ($logs as $log)

                            @php
                                $logStatusLabels = [
                                    'pending' => 'Menunggu Konfirmasi',
                                    'generated' => 'Berhasil',
                                    'skipped' => 'Dilewati',
                                    'failed' => 'Gagal',
                                    'cancelled' => 'Dibatalkan',
                                ];

                                $logStatusClasses = [
                                    'pending' => 'bg-amber-50 text-amber-700',
                                    'generated' => 'bg-emerald-50 text-emerald-700',
                                    'skipped' => 'bg-slate-100 text-slate-600',
                                    'failed' => 'bg-red-50 text-red-700',
                                    'cancelled' => 'bg-slate-100 text-slate-600',
                                ];
                            @endphp


                            <tr class="hover:bg-slate-50">

                                {{-- TANGGAL JADWAL --}}
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">
                                    {{ $log->scheduled_date?->format('d/m/Y') ?? '-' }}
                                </td>


                                {{-- TANGGAL TRANSAKSI --}}
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">
                                    {{ $log->transaction_date?->format('d/m/Y') ?? '-' }}
                                </td>


                                {{-- NOMINAL --}}
                                <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-semibold text-slate-800">
                                    Rp {{ number_format((float) $log->amount, 0, ',', '.') }}
                                </td>


                                {{-- STATUS --}}
                                <td class="whitespace-nowrap px-5 py-4 text-center">

                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $logStatusClasses[$log->status] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ $logStatusLabels[$log->status] ?? ucfirst($log->status) }}
                                    </span>

                                </td>


                                {{-- KETERANGAN --}}
                                <td class="px-5 py-4 text-sm text-slate-600">

                                    @if ($log->status === 'generated')

                                        @if ($log->transaction_id)
                                            Transaksi #{{ $log->transaction_id }}
                                        @else
                                            Transaksi berhasil dibuat
                                        @endif

                                    @elseif ($log->status === 'pending')

                                        Menunggu konfirmasi pengguna.

                                    @elseif ($log->notes)

                                        {{ $log->notes }}

                                    @else

                                        -

                                    @endif

                                    @if ($log->generated_at)
                                        <p class="mt-1 text-xs text-slate-400">
                                            Dibuat: {{ $log->generated_at->format('d/m/Y H:i') }}
                                        </p>
                                    @endif

                                    @if ($log->confirmed_at)
                                        <p class="mt-1 text-xs text-slate-400">
                                            Dikonfirmasi: {{ $log->confirmed_at->format('d/m/Y H:i') }}
                                        </p>
                                    @endif

                                </td>


                                {{-- AKSI --}}
                                <td class="px-5 py-4">

                                    @if (
                                        $log->status === 'pending'
                                        && $recurringTransaction->status === 'active'
                                        && !$recurringTransaction->trashed()
                                    )

                                        <div class="flex flex-col items-center gap-2">

                                            {{-- FORM KONFIRMASI --}}
                                            <form method="POST"
                                                  action="{{ route('recurring-transactions.confirm', $log) }}"
                                                  class="flex flex-col gap-2">

                                                @csrf

                                                <input type="number"
                                                       name="amount"
                                                       value="{{ $log->amount }}"
                                                       min="0.01"
                                                       step="0.01"
                                                       required
                                                       aria-label="Nominal aktual"
                                                       class="w-32 rounded-lg border border-slate-300 px-2 py-1.5 text-right text-xs outline-none focus:border-emerald-500">

                                                <button type="submit"
                                                        onclick="return confirm('Konfirmasi dan catat transaksi ini?')"
                                                        class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">
                                                    Konfirmasi
                                                </button>

                                            </form>


                                            {{-- FORM BATALKAN --}}
                                            <form method="POST"
                                                  action="{{ route('recurring-transactions.cancel', $log) }}">

                                                @csrf

                                                <button type="submit"
                                                        onclick="return confirm('Batalkan transaksi Pending ini?')"
                                                        class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">
                                                    Batalkan
                                                </button>

                                            </form>

                                        </div>

                                    @else

                                        <span class="text-xs text-slate-400">-</span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">

                                    <p class="text-sm font-medium text-slate-500">
                                        Belum ada riwayat transaksi.
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        Riwayat akan muncul setelah jadwal diproses oleh sistem.
                                    </p>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- PAGINATION --}}
            @if ($logs->hasPages())

                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $logs->links() }}
                </div>

            @endif

        </div>

    </div>

</x-finance-layout>
