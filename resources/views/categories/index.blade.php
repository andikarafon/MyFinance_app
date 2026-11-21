
<x-finance-layout title="Master Kategori">

    {{-- =====================================================
         HEADER
    ====================================================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

        <div>
            <h2 class="text-xl font-bold text-slate-800">
                Master Kategori
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Kelola kategori pemasukan dan pengeluaran Anda.
            </p>
        </div>

        <a href="{{ route('categories.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg shadow-sm transition">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-5 h-5"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 stroke-width="2">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M12 5v14m-7-7h14" />

            </svg>

            Tambah Kategori

        </a>

    </div>


    {{-- =====================================================
         NOTIFICATION
    ====================================================== --}}
    @if (session('success'))

        <div class="mb-6 flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-5 h-5 shrink-0"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 stroke-width="2">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="m5 13 4 4L19 7" />

            </svg>

            {{ session('success') }}

        </div>

    @endif



    {{-- STATISTICS --}}
    <div
        class="mb-6"
        style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px;"
    >

        {{-- TOTAL KATEGORI --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 min-w-0">

            <p class="text-sm text-slate-500 mb-2">
                Total Kategori
            </p>

            <h3 class="text-2xl font-bold text-slate-900">
                {{ $categories->count() }}
            </h3>

        </div>


        {{-- KATEGORI PEMASUKAN --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 min-w-0">

            <p class="text-sm text-slate-500 mb-2">
                Kategori Pemasukan
            </p>

            <h3 class="text-2xl font-bold text-emerald-600">
                {{ $totalIncome }}
            </h3>

        </div>


        {{-- KATEGORI PENGELUARAN --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 min-w-0">

            <p class="text-sm text-slate-500 mb-2">
                Kategori Pengeluaran
            </p>

            <h3 class="text-2xl font-bold text-rose-500">
                {{ $totalExpense }}
            </h3>

        </div>

    </div>



    {{-- =====================================================
         CATEGORY TABLE
    ====================================================== --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- TABLE HEADER --}}
        <div class="px-6 py-5 border-b border-slate-200">

            <h3 class="text-base font-semibold text-slate-800">
                Daftar Kategori
            </h3>

            <p class="text-sm text-slate-500 mt-1">
                Daftar seluruh kategori yang telah Anda buat.
            </p>

        </div>


        {{-- TABLE CONTENT --}}
        <div class="overflow-x-auto">

            <table class="w-full text-sm text-left">

                <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">

                    <tr>

                        <th class="px-6 py-4 font-semibold">
                            Kategori
                        </th>

                        <th class="px-6 py-4 font-semibold">
                            Jenis
                        </th>

                        <th class="px-6 py-4 font-semibold">
                            Warna
                        </th>

                        <th class="px-6 py-4 font-semibold">
                            Deskripsi
                        </th>

                        <th class="px-6 py-4 font-semibold text-center">
                            Status
                        </th>

                        <th class="px-6 py-4 font-semibold text-center">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse ($categories as $category)

                        <tr class="hover:bg-slate-50 transition">

                            {{-- KATEGORI --}}
                            <td class="px-6 py-4">

                                <div class="flex items-center gap-3">

                                    <div
                                        class="category-color w-10 h-10 rounded-lg flex items-center justify-center text-lg shrink-0"
                                        data-color="{{ $category->color }}"
                                    >
                                        {{ $category->icon }}
                                    </div>

                                    <div>
                                        <p class="font-semibold text-slate-800">
                                            {{ $category->name }}
                                        </p>
                                    </div>

                                </div>

                            </td>


                            {{-- JENIS --}}
                            <td class="px-6 py-4">

                                @if ($category->type === 'income')

                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">

                                        Pemasukan

                                    </span>

                                @else

                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700">

                                        Pengeluaran

                                    </span>

                                @endif

                            </td>


                            {{-- WARNA --}}
                            <td class="px-6 py-4">

                                <div class="flex items-center gap-2">

                                    <span
                                        class="category-color-dot w-5 h-5 rounded-full border border-slate-200"
                                        data-color="{{ $category->color }}"
                                    ></span>

                                    <span class="text-xs text-slate-500">
                                        {{ $category->color }}
                                    </span>

                                </div>

                            </td>


                            {{-- DESKRIPSI --}}
                            <td class="px-6 py-4 text-slate-500 max-w-xs">

                                <span class="block truncate">
                                    {{ $category->description ?: '-' }}
                                </span>

                            </td>


                            {{-- STATUS --}}
                            <td class="px-6 py-4 text-center">

                                @if ($category->is_active)

                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">

                                        Aktif

                                    </span>

                                @else

                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500">

                                        Nonaktif

                                    </span>

                                @endif

                            </td>


                            {{-- AKSI --}}
                            <td class="px-6 py-4">

                                <div class="flex items-center justify-center gap-2">

                                    {{-- EDIT --}}
                                    <a href="{{ route('categories.edit', $category) }}"
                                       class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-blue-600 hover:bg-blue-50 transition"
                                       title="Edit Kategori">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="w-4 h-4"
                                             fill="none"
                                             viewBox="0 0 24 24"
                                             stroke="currentColor"
                                             stroke-width="2">

                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  d="m16.862 4.487 1.687-1.688a2.25 2.25 0 1 1 3.182 3.182l-9.75 9.75a4.5 4.5 0 0 1-1.897 1.13l-3.66 1.098 1.098-3.66a4.5 4.5 0 0 1 1.13-1.897l8.21-7.915Z" />

                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  d="M19.5 7.125 16.875 4.5" />

                                        </svg>

                                    </a>


                                    {{-- DELETE --}}
                                    <form
                                        method="POST"
                                        action="{{ route('categories.destroy', $category) }}"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $category->name }}?')"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-rose-600 hover:bg-rose-50 transition"
                                            title="Hapus Kategori"
                                        >

                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 class="w-4 h-4"
                                                 fill="none"
                                                 viewBox="0 0 24 24"
                                                 stroke="currentColor"
                                                 stroke-width="2">

                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      d="M6 7h12m-10 0V5a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m2 0-.8 12.1a2 2 0 0 1-2 1.9H8.8a2 2 0 0 1-2-1.9L6 7Zm4 4v6m4-6v6" />

                                            </svg>

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6" class="px-6 py-16 text-center">

                                <div class="flex flex-col items-center justify-center">

                                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="w-8 h-8 text-slate-400"
                                             fill="none"
                                             viewBox="0 0 24 24"
                                             stroke="currentColor"
                                             stroke-width="1.5">

                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  d="M20 12 12 20l-8-8V4h8l8 8Z" />

                                            <circle cx="8.5" cy="8.5" r="1" />

                                        </svg>

                                    </div>

                                    <h4 class="text-base font-semibold text-slate-700">
                                        Belum Ada Kategori
                                    </h4>

                                    <p class="text-sm text-slate-500 mt-1 mb-4">
                                        Silakan tambahkan kategori pertama Anda.
                                    </p>

                                    <a href="{{ route('categories.create') }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">

                                        <span>Tambah Kategori</span>

                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if (method_exists($categories, 'links'))

            @if ($categories->hasPages())

                <div class="px-6 py-4 border-t border-slate-200">
                    {{ $categories->links() }}
                </div>

            @endif

        @endif

    </div>


    {{-- =====================================================
         DYNAMIC CATEGORY COLORS
    ====================================================== --}}
    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const colorElements = document.querySelectorAll('[data-color]');

            colorElements.forEach(function (element) {

                const color = element.dataset.color;

                // Hanya menerima format HEX warna.
                const isValidColor = /^#[0-9A-Fa-f]{6}$/.test(color);

                if (isValidColor) {

                    if (element.classList.contains('category-color')) {
                        element.style.setProperty(
                            'background-color',
                            color + '20'
                        );
                    }

                    if (element.classList.contains('category-color-dot')) {
                        element.style.setProperty(
                            'background-color',
                            color
                        );
                    }

                }

            });

        });

    </script>

</x-finance-layout>
