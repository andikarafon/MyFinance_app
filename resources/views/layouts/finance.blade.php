
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - MyFinance App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">

<div class="min-h-screen md:flex">

    <!-- Sidebar -->
    <aside class="w-full md:w-64 bg-slate-900 text-white flex-shrink-0">
        <div class="p-6 border-b border-slate-700">
            <div class="text-xl font-bold">MyFinance</div>
            <div class="text-xs text-slate-400 mt-1">
                Personal Finance Management
            </div>
        </div>

        <nav class="p-4 space-y-2">
            <div class="text-xs uppercase text-slate-500 mb-3">
                Menu Utama
            </div>

            <a href="{{ route('dashboard') }}"
               class="block px-4 py-3 rounded-lg bg-emerald-600 text-white">
                Dashboard
            </a>

            <a href="#" class="block px-4 py-3 rounded-lg hover:bg-slate-800">
                Transaksi
            </a>

            <a href="#" class="block px-4 py-3 rounded-lg hover:bg-slate-800">
                Rekening
            </a>

            <a href="#" class="block px-4 py-3 rounded-lg hover:bg-slate-800">
                Kategori
            </a>

            <div class="text-xs uppercase text-slate-500 mt-6 mb-3">
                Perencanaan
            </div>

            <a href="#" class="block px-4 py-3 rounded-lg hover:bg-slate-800">
                Anggaran
            </a>

            <a href="#" class="block px-4 py-3 rounded-lg hover:bg-slate-800">
                Target Tabungan
            </a>

            <div class="text-xs uppercase text-slate-500 mt-6 mb-3">
                Analisis
            </div>

            <a href="#" class="block px-4 py-3 rounded-lg hover:bg-slate-800">
                Laporan Keuangan
            </a>

            <a href="#" class="block px-4 py-3 rounded-lg hover:bg-slate-800">
                Export Excel
            </a>
        </nav>

        <div class="p-4 border-t border-slate-700 mt-4">
            <div class="text-sm font-medium">
                {{ auth()->user()->name }}
            </div>
            <div class="text-xs text-slate-400 mb-3">
                {{ auth()->user()->email }}
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="text-sm text-red-400 hover:text-red-300">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 min-w-0">

        <header class="bg-white border-b border-gray-200 px-6 py-5">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-semibold">
                        {{ $title ?? 'Dashboard' }}
                    </h1>
                    <p class="text-sm text-gray-500">
                        Selamat datang di MyFinance App
                    </p>
                </div>

                <div class="text-sm text-gray-500">
                    {{ now()->translatedFormat('d F Y') }}
                </div>
            </div>
        </header>

        <div class="p-6">
            {{ $slot }}
        </div>

    </main>
</div>

</body>
</html>
