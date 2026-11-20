
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} - MyFinance App</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-800 antialiased">

<div class="min-h-screen md:flex">

    <!-- ============================================== -->
    <!-- SIDEBAR -->
    <!-- ============================================== -->

    <aside class="w-full md:w-64 bg-slate-900 text-white flex-shrink-0">

        <!-- BRAND -->
        <div class="p-6 border-b border-slate-700">

            <div class="flex items-center gap-3">

                <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center font-bold text-xl">
                    M
                </div>

                <div>
                    <div class="text-xl font-bold">
                        MyFinance
                    </div>

                    <div class="text-xs text-slate-400">
                        Personal Finance Management
                    </div>
                </div>

            </div>

        </div>


        <!-- NAVIGATION -->
        <nav class="p-4 space-y-2">

            <div class="text-xs uppercase tracking-wider text-slate-500 mb-3 px-3">
                Menu Utama
            </div>

            <!-- DASHBOARD -->
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg
               {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">

                <span>▦</span>
                <span>Dashboard</span>

            </a>

            <!-- TRANSAKSI -->
            <a href="#"
               class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800">

                <span>⇄</span>
                <span>Transaksi</span>

            </a>

            <!-- REKENING -->
            <a href="{{ route('accounts.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-lg
            {{ request()->routeIs('accounts.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">

                <span>▣</span>
                <span>Rekening</span>

            </a>

            <!-- KATEGORI -->

            <a href="{{ route('categories.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg
                {{ request()->routeIs('categories.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">

                    <span>▤</span>
                    <span>Kategori</span>

            </a>



            <!-- PERENCANAAN -->
            <div class="text-xs uppercase tracking-wider text-slate-500 mt-6 mb-3 px-3">
                Perencanaan
            </div>

            <!-- ANGGARAN -->
            <a href="#"
               class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800">

                <span>▧</span>
                <span>Anggaran</span>

            </a>

            <!-- TARGET TABUNGAN -->
            <a href="#"
               class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800">

                <span>◎</span>
                <span>Target Tabungan</span>

            </a>


            <!-- ANALISIS -->
            <div class="text-xs uppercase tracking-wider text-slate-500 mt-6 mb-3 px-3">
                Analisis
            </div>

            <!-- LAPORAN -->
            <a href="#"
               class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800">

                <span>▥</span>
                <span>Laporan Keuangan</span>

            </a>

            <!-- EXPORT EXCEL -->
            <a href="#"
               class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800">

                <span>⇩</span>
                <span>Export Excel</span>

            </a>


            <!-- SISTEM -->
            <div class="text-xs uppercase tracking-wider text-slate-500 mt-6 mb-3 px-3">
                Sistem
            </div>

            <!-- PENGATURAN -->
            <a href="#"
               class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800">

                <span>⚙</span>
                <span>Pengaturan</span>

            </a>

        </nav>


        <!-- ============================================== -->
        <!-- USER PROFILE -->
        <!-- ============================================== -->

        <div class="p-4 border-t border-slate-700 mt-4">

            <!-- USER INFORMATION -->
            <div class="flex items-center gap-3 mb-4">

                <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center font-semibold">

                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}

                </div>

                <div class="min-w-0">

                    <div class="text-sm font-medium truncate">
                        {{ auth()->user()->name }}
                    </div>

                    <div class="text-xs text-slate-400 truncate">
                        {{ auth()->user()->email }}
                    </div>

                </div>

            </div>


            <!-- CHANGE PASSWORD BUTTON -->
            <button type="button"
                    onclick="openPasswordModal()"
                    class="w-full flex items-center gap-3 text-sm text-slate-300 hover:text-white hover:bg-slate-800 px-3 py-2 rounded-lg transition">

                <span>🔒</span>
                <span>Change Password</span>

            </button>


            <!-- LOGOUT -->
            <form method="POST" action="{{ route('logout') }}" class="mt-2">

                @csrf

                <button type="submit"
                        class="w-full text-left flex items-center gap-3 text-sm text-red-400 hover:text-red-300 hover:bg-slate-800 px-3 py-2 rounded-lg transition">

                    <span>↪</span>
                    <span>Logout</span>

                </button>

            </form>

        </div>

    </aside>


    <!-- ============================================== -->
    <!-- MAIN CONTENT -->
    <!-- ============================================== -->

    <main class="flex-1 min-w-0">


        <!-- HEADER -->
        <header class="bg-white border-b border-gray-200 px-6 py-5">

            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">

                <div>

                    <h1 class="text-xl font-semibold text-gray-800">
                        {{ $title ?? 'Dashboard' }}
                    </h1>

                    <p class="text-sm text-gray-500 mt-1">
                        Selamat datang di MyFinance App
                    </p>

                </div>


                <!-- CURRENT DATE -->
                <div class="flex items-center gap-2 text-sm text-gray-500">

                    <span>📅</span>

                    <span>
                        {{ now()->translatedFormat('d F Y') }}
                    </span>

                </div>

            </div>

        </header>


        <!-- PAGE CONTENT -->
        <div class="p-4 sm:p-6 lg:p-8">

            {{ $slot }}

        </div>


        <!-- FOOTER -->
        <footer class="px-6 py-5 border-t border-gray-200 text-center text-xs text-gray-400">

            MyFinance App &copy; {{ date('Y') }}.
            All Rights Reserved.

        </footer>

    </main>

</div>


<!-- ============================================== -->
<!-- CHANGE PASSWORD MODAL -->
<!-- ============================================== -->

<div id="passwordModal"
     data-reopen="{{ $errors->getBag('updatePassword')->any() ? '1' : '0' }}"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">

    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden">


        <!-- MODAL HEADER -->
        <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">

            <div>

                <h2 class="text-lg font-semibold text-gray-800">
                    Change Password
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Update your account password
                </p>

            </div>

            <button type="button"
                    onclick="closePasswordModal()"
                    class="text-gray-400 hover:text-gray-700 text-2xl">

                &times;

            </button>

        </div>


        <!-- MODAL BODY -->
        <form method="POST" action="{{ route('password.update') }}">

            @csrf

            @method('PUT')


            <div class="p-6 space-y-5">


                <!-- CURRENT PASSWORD -->
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Current Password
                    </label>

                    <div class="relative">

                        <input type="password"
                               name="current_password"
                               id="current_password"
                               required
                               autocomplete="current-password"
                               placeholder="Enter current password"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-16 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">

                        <button type="button"
                                onclick="togglePassword('current_password', this)"
                                class="absolute right-3 top-3 text-xs text-gray-500 hover:text-emerald-600">

                            Show

                        </button>

                    </div>

                    @error('current_password', 'updatePassword')

                        <p class="text-xs text-red-500 mt-2">
                            {{ $message }}
                        </p>

                    @enderror

                </div>



                <!-- NEW PASSWORD -->
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        New Password
                    </label>

                    <div class="relative">

                        <input type="password"
                               name="password"
                               id="new_password"
                               required
                               minlength="8"
                               autocomplete="new-password"
                               placeholder="Enter new password"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-16 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">

                        <button type="button"
                                onclick="togglePassword('new_password', this)"
                                class="absolute right-3 top-3 text-xs text-gray-500 hover:text-emerald-600">

                            Show

                        </button>

                    </div>

                    @error('password', 'updatePassword')

                        <p class="text-xs text-red-500 mt-2">
                            {{ $message }}
                        </p>

                    @enderror

                    <p class="text-xs text-gray-400 mt-2">
                        Minimum 8 characters.
                    </p>

                </div>



                <!-- CONFIRM PASSWORD -->
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm New Password
                    </label>

                    <div class="relative">

                        <input type="password"
                               name="password_confirmation"
                               id="confirm_password"
                               required
                               minlength="8"
                               autocomplete="new-password"
                               placeholder="Confirm new password"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-16 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">

                        <button type="button"
                                onclick="togglePassword('confirm_password', this)"
                                class="absolute right-3 top-3 text-xs text-gray-500 hover:text-emerald-600">

                            Show

                        </button>

                    </div>

                </div>


            </div>



            <!-- MODAL FOOTER -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">


                <!-- CANCEL -->
                <button type="button"
                        onclick="closePasswordModal()"
                        class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition">

                    Cancel

                </button>


                <!-- UPDATE PASSWORD -->
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition">

                    Update Password

                </button>

            </div>

        </form>

    </div>

</div>



<!-- ============================================== -->
<!-- JAVASCRIPT -->
<!-- ============================================== -->

<script>

    // ==========================================
    // OPEN PASSWORD MODAL
    // ==========================================

    function openPasswordModal() {

        const modal = document.getElementById('passwordModal');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

    }


    // ==========================================
    // CLOSE PASSWORD MODAL
    // ==========================================

    function closePasswordModal() {

        const modal = document.getElementById('passwordModal');

        modal.classList.add('hidden');
        modal.classList.remove('flex');

    }


    // ==========================================
    // SHOW / HIDE PASSWORD
    // ==========================================

    function togglePassword(id, button) {

        const input = document.getElementById(id);

        if (input.type === 'password') {

            input.type = 'text';
            button.innerText = 'Hide';

        } else {

            input.type = 'password';
            button.innerText = 'Show';

        }

    }


    // ==========================================
    // CLOSE WHEN CLICKING OUTSIDE MODAL
    // ==========================================

    document.getElementById('passwordModal').addEventListener('click', function(e) {

        if (e.target === this) {

            closePasswordModal();

        }

    });


    // ==========================================
    // CLOSE WITH ESC KEY
    // ==========================================

    document.addEventListener('keydown', function(e) {

        if (e.key === 'Escape') {

            closePasswordModal();

        }

    });


    // ==========================================
    // REOPEN MODAL IF VALIDATION FAILED
    // ==========================================

    const passwordModal = document.getElementById('passwordModal');

    if (passwordModal.dataset.reopen === '1') {

        openPasswordModal();

    }


</script>


</body>
</html>
