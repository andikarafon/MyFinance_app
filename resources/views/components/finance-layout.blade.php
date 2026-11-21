
@props(['title' => 'MyFinance App'])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title }} - MyFinance App</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="bg-slate-100 text-slate-800"
    data-current-route="{{ request()->route()?->getName() }}"
>

    <div class="min-h-screen flex">

        {{-- =====================================================
             SIDEBAR
        ====================================================== --}}
        <aside
            id="sidebar"
            class="w-64 min-h-screen bg-slate-900 text-white flex flex-col transition-all duration-300 shrink-0"
        >

            {{-- BRAND --}}
            <div class="h-20 flex items-center px-6 border-b border-slate-800 shrink-0">

                <div class="flex items-center gap-3">

                    <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-6 h-6 text-white"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M12 8c-2.5 0-4 1-4 2.5S9.5 13 12 13s4 1 4 2.5S14.5 18 12 18m0-10V6m0 14v-2M4 5h16M4 19h16" />

                        </svg>

                    </div>

                    <div>
                        <h1 class="text-lg font-bold tracking-wide">
                            MyFinance
                        </h1>

                        <p class="text-xs text-slate-400">
                            Personal Finance Manager
                        </p>
                    </div>

                </div>

            </div>


            {{-- =====================================================
                 NAVIGATION
            ====================================================== --}}
            <nav class="flex-1 px-3 py-5 space-y-5 overflow-y-auto">

                {{-- DASHBOARD --}}
                <div>

                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                       {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-5 h-5 shrink-0"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M3 10.5 12 3l9 7.5M5 9v11h14V9M9 20v-6h6v6" />

                        </svg>

                        <span class="text-sm font-medium">
                            Dashboard
                        </span>

                    </a>

                </div>


                {{-- =====================================================
                     TRANSAKSI
                ====================================================== --}}
                <div>

                    {{-- TRANSAKSI HEADER --}}
                    <button
                        type="button"
                        onclick="toggleMenuGroup('transactionMenu', 'transactionArrow')"
                        class="w-full flex items-center justify-between px-4 py-2 text-xs font-semibold uppercase tracking-wider text-slate-500 hover:text-slate-300 transition"
                        aria-controls="transactionMenu"
                        aria-expanded="true"
                    >

                        <span>Transaksi</span>

                        <svg id="transactionArrow"
                             xmlns="http://www.w3.org/2000/svg"
                             class="w-4 h-4 transition-transform duration-200 rotate-180"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="m6 9 6 6 6-6" />

                        </svg>

                    </button>


                    {{-- TRANSAKSI CHILD MENU --}}
                    <div id="transactionMenu" class="mt-1 space-y-1">

                        {{-- PEMASUKAN --}}
                        <a href="{{ route('transactions.income') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                           {{ request()->routeIs('transactions.income') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="w-5 h-5 shrink-0"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M7 17 17 7M7 7h10v10" />

                            </svg>

                            <span class="text-sm font-medium">
                                Pemasukan
                            </span>

                        </a>


                        {{-- PENGELUARAN --}}
                        <a href="{{ route('transactions.expense') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                           {{ request()->routeIs('transactions.expense') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="w-5 h-5 shrink-0"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M17 7 7 17m0-10v10h10" />

                            </svg>

                            <span class="text-sm font-medium">
                                Pengeluaran
                            </span>

                        </a>


                        {{-- TRANSFER REKENING --}}
                        <a href="{{ route('transactions.transfer') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                           {{ request()->routeIs('transactions.transfer') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="w-5 h-5 shrink-0"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M17 3l4 4-4 4M3 7h18M7 21l-4-4 4-4m14 4H3" />

                            </svg>

                            <span class="text-sm font-medium">
                                Transfer Rekening
                            </span>

                        </a>

                    </div>

                </div>


                {{-- =====================================================
                     MASTER
                ====================================================== --}}
                <div>

                    {{-- MASTER HEADER --}}
                    <button
                        type="button"
                        onclick="toggleMenuGroup('masterMenu', 'masterArrow')"
                        class="w-full flex items-center justify-between px-4 py-2 text-xs font-semibold uppercase tracking-wider text-slate-500 hover:text-slate-300 transition"
                        aria-controls="masterMenu"
                        aria-expanded="true"
                    >

                        <span>Master</span>

                        <svg id="masterArrow"
                             xmlns="http://www.w3.org/2000/svg"
                             class="w-4 h-4 transition-transform duration-200 rotate-180"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="m6 9 6 6 6-6" />

                        </svg>

                    </button>


                    {{-- MASTER CHILD MENU --}}
                    <div id="masterMenu" class="mt-1 space-y-1">

                        {{-- REKENING --}}
                        <a href="{{ route('accounts.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                           {{ request()->routeIs('accounts.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="w-5 h-5 shrink-0"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">

                                <rect x="3" y="5" width="18" height="14" rx="2" />

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M3 10h18M7 15h3" />

                            </svg>

                            <span class="text-sm font-medium">
                                Rekening
                            </span>

                        </a>


                        {{-- KATEGORI --}}
                        <a href="{{ route('categories.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                           {{ request()->routeIs('categories.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="w-5 h-5 shrink-0"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M20 12 12 20l-8-8V4h8l8 8Z" />

                                <circle cx="8.5" cy="8.5" r="1" />

                            </svg>

                            <span class="text-sm font-medium">
                                Kategori
                            </span>

                        </a>

                    </div>

                </div>

            </nav>


            {{-- =====================================================
                 USER PROFILE
            ====================================================== --}}
            <div class="border-t border-slate-800 p-4 shrink-0">

                {{-- USER INFO --}}
                <div class="flex items-center gap-3 mb-4">

                    <div class="w-10 h-10 rounded-full bg-emerald-600 flex items-center justify-center text-white font-bold shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>

                    <div class="min-w-0">

                        <p class="text-sm font-semibold text-white truncate">
                            {{ Auth::user()->name }}
                        </p>

                        <p class="text-xs text-slate-400 truncate">
                            {{ Auth::user()->email }}
                        </p>

                    </div>

                </div>


                {{-- USER ACTIONS --}}
                <div class="space-y-1">

                    {{-- CHANGE PASSWORD --}}
                    <button
                        type="button"
                        onclick="openPasswordModal()"
                        class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800 hover:text-white transition"
                    >

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-5 h-5 shrink-0"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6Zm2-10V7a4 4 0 1 1 8 0v4" />

                        </svg>

                        <span>Change Password</span>

                    </button>


                    {{-- LOGOUT --}}
                    <form method="POST" action="{{ route('logout') }}">

                        @csrf

                        <button
                            type="submit"
                            class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-red-400 hover:bg-red-500/10 transition"
                        >

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="w-5 h-5 shrink-0"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M15 17l5-5-5-5M20 12H9m4 5v1a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2v1" />

                            </svg>

                            <span>Logout</span>

                        </button>

                    </form>

                </div>

            </div>

        </aside>


        {{-- =====================================================
             MAIN CONTENT
        ====================================================== --}}
        <div class="flex-1 min-w-0">

            {{-- HEADER --}}
            <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-6 lg:px-8">

                <div class="flex items-center gap-4">

                    {{-- SIDEBAR TOGGLE --}}
                    <button
                        type="button"
                        onclick="toggleSidebar()"
                        class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 transition"
                        title="Hide / Show Sidebar"
                    >

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-6 h-6"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M4 6h16M4 12h16M4 18h16" />

                        </svg>

                    </button>


                    {{-- PAGE TITLE --}}
                    <div>

                        <h2 class="text-lg font-semibold text-slate-800">
                            {{ $title }}
                        </h2>

                        <p class="text-xs text-slate-500">
                            MyFinance App
                        </p>

                    </div>

                </div>


                {{-- CURRENT DATE --}}
                <div class="text-sm text-slate-500 hidden sm:block">
                    {{ now()->translatedFormat('l, d F Y') }}
                </div>

            </header>


            {{-- PAGE CONTENT --}}
            <main class="p-6 lg:p-8">

                {{ $slot }}

            </main>

        </div>

    </div>


    {{-- =====================================================
         CHANGE PASSWORD MODAL
    ====================================================== --}}
    <div
        id="passwordModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 p-4"
    >

        <div class="w-full max-w-md rounded-2xl bg-white shadow-xl">

            {{-- MODAL HEADER --}}
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">

                <h3 class="text-lg font-semibold text-slate-800">
                    Change Password
                </h3>

                <button
                    type="button"
                    onclick="closePasswordModal()"
                    class="text-slate-400 hover:text-slate-700 text-2xl"
                >
                    &times;
                </button>

            </div>


            {{-- MODAL FORM --}}
            <form
                method="POST"
                action="{{ route('password.update') }}"
                class="p-6 space-y-4"
            >

                @csrf
                @method('PUT')


                {{-- VALIDATION ERRORS --}}
                @if ($errors->updatePassword->any())

                    <div class="rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-600">

                        @foreach ($errors->updatePassword->all() as $error)

                            <p>{{ $error }}</p>

                        @endforeach

                    </div>

                @endif


                {{-- CURRENT PASSWORD --}}
                <div>

                    <label
                        for="current_password"
                        class="block text-sm font-medium text-slate-700 mb-1"
                    >
                        Current Password
                    </label>

                    <input
                        type="password"
                        name="current_password"
                        id="current_password"
                        required
                        autocomplete="current-password"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none"
                    >

                </div>


                {{-- NEW PASSWORD --}}
                <div>

                    <label
                        for="new_password"
                        class="block text-sm font-medium text-slate-700 mb-1"
                    >
                        New Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        id="new_password"
                        required
                        autocomplete="new-password"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none"
                    >

                </div>


                {{-- CONFIRM PASSWORD --}}
                <div>

                    <label
                        for="password_confirmation"
                        class="block text-sm font-medium text-slate-700 mb-1"
                    >
                        Confirm New Password
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        required
                        autocomplete="new-password"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none"
                    >

                </div>


                {{-- SHOW PASSWORD --}}
                <label class="flex items-center gap-2 text-sm text-slate-600">

                    <input
                        type="checkbox"
                        id="showPassword"
                        onchange="togglePasswordVisibility()"
                        class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                    >

                    Tampilkan password

                </label>


                {{-- MODAL ACTIONS --}}
                <div class="flex justify-end gap-3 pt-3">

                    <button
                        type="button"
                        onclick="closePasswordModal()"
                        class="px-4 py-2 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700"
                    >
                        Update Password
                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- =====================================================
         JAVASCRIPT
    ====================================================== --}}
    <script>

        /**
         * SIDEBAR HIDE / SHOW
         */
        function toggleSidebar() {

            const sidebar = document.getElementById('sidebar');

            if (sidebar) {
                sidebar.classList.toggle('hidden');
            }

        }


        /**
         * TRANSAKSI / MASTER ACCORDION
         */
        function toggleMenuGroup(menuId, arrowId) {

            const menu = document.getElementById(menuId);
            const arrow = document.getElementById(arrowId);

            if (!menu || !arrow) {
                return;
            }

            const isHidden = menu.classList.toggle('hidden');

            arrow.classList.toggle('rotate-180', !isHidden);

            // Update accessibility state.
            const button = document.querySelector(
                '[aria-controls="' + menuId + '"]'
            );

            if (button) {
                button.setAttribute(
                    'aria-expanded',
                    String(!isHidden)
                );
            }

            // Simpan status menu.
            try {
                localStorage.setItem(
                    menuId,
                    isHidden ? 'closed' : 'open'
                );
            } catch (error) {
                // Abaikan jika localStorage tidak tersedia.
            }

        }


        /**
         * INITIALIZE ACCORDION
         */
        document.addEventListener('DOMContentLoaded', function () {

        const passwordModal = document.getElementById('passwordModal');

        if (passwordModal) {
            const errorElement = passwordModal.querySelector('.bg-red-50');

            if (errorElement) {
                openPasswordModal();
            }
        }

        });


        /**
         * CHANGE PASSWORD MODAL
         */
        function openPasswordModal() {

            const modal = document.getElementById('passwordModal');

            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

        }


        function closePasswordModal() {

            const modal = document.getElementById('passwordModal');

            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

        }


        /**
         * SHOW / HIDE PASSWORD
         */
        function togglePasswordVisibility() {

            const checkbox = document.getElementById('showPassword');

            const fields = [
                document.getElementById('current_password'),
                document.getElementById('new_password'),
                document.getElementById('password_confirmation')
            ];

            fields.forEach(function (field) {

                if (field) {
                    field.type = checkbox.checked ? 'text' : 'password';
                }

            });

        }


        /**
         * CLOSE MODAL WHEN CLICKING OUTSIDE
         */
        document.addEventListener('click', function (event) {

            const modal = document.getElementById('passwordModal');

            if (modal && event.target === modal) {
                closePasswordModal();
            }

        });


        /**
         * OPEN MODAL IF VALIDATION ERROR
         */
        document.addEventListener('DOMContentLoaded', function () {

            const hasPasswordErrors =
                document.getElementById('passwordModal')
                    ?.querySelector('.bg-red-50') !== null;

            if (hasPasswordErrors) {
                openPasswordModal();
            }

        });

    </script>

</body>
</html>
