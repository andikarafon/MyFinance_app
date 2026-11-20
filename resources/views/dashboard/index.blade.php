
<x-finance-layout>
    <x-slot name="title">
        Dashboard
    </x-slot>

    <!-- SUMMARY CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

        <!-- SALDO -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <p class="text-sm text-gray-500">Total Saldo</p>
            <h2 class="text-2xl font-bold text-gray-800 mt-3">
                Rp 0
            </h2>
            <p class="text-xs text-gray-400 mt-2">
                Seluruh rekening
            </p>
        </div>

        <!-- PEMASUKAN -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <p class="text-sm text-gray-500">Total Pemasukan</p>
            <h2 class="text-2xl font-bold text-emerald-600 mt-3">
                Rp 0
            </h2>
            <p class="text-xs text-gray-400 mt-2">
                Bulan ini
            </p>
        </div>

        <!-- PENGELUARAN -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <p class="text-sm text-gray-500">Total Pengeluaran</p>
            <h2 class="text-2xl font-bold text-red-500 mt-3">
                Rp 0
            </h2>
            <p class="text-xs text-gray-400 mt-2">
                Bulan ini
            </p>
        </div>

        <!-- ANGGARAN -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <p class="text-sm text-gray-500">Sisa Anggaran</p>
            <h2 class="text-2xl font-bold text-blue-600 mt-3">
                Rp 0
            </h2>
            <p class="text-xs text-gray-400 mt-2">
                Bulan ini
            </p>
        </div>

    </div>

    <!-- CHART AND TRANSACTION -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mt-6">

        <div class="xl:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-800">
                Grafik Keuangan
            </h3>

            <p class="text-sm text-gray-400 mt-1">
                Ringkasan pemasukan dan pengeluaran
            </p>

            <div class="h-64 flex items-center justify-center text-gray-400">
                Belum ada data transaksi
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-800">
                Transaksi Terakhir
            </h3>

            <p class="text-sm text-gray-400 mt-1">
                Riwayat transaksi terbaru
            </p>

            <div class="h-64 flex items-center justify-center text-gray-400">
                Belum ada transaksi
            </div>
        </div>

    </div>

</x-finance-layout>
