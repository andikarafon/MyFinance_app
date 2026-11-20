
<x-finance-layout>
    <x-slot name="title">Master Kategori</x-slot>

    <!-- SUMMARY -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">

        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <p class="text-sm text-gray-500">Total Kategori</p>
            <h2 class="text-2xl font-bold mt-2">
                {{ $categories->count() }}
            </h2>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <p class="text-sm text-gray-500">Kategori Pemasukan</p>
            <h2 class="text-2xl font-bold text-emerald-600 mt-2">
                {{ $totalIncome }}
            </h2>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <p class="text-sm text-gray-500">Kategori Pengeluaran</p>
            <h2 class="text-2xl font-bold text-red-500 mt-2">
                {{ $totalExpense }}
            </h2>
        </div>

    </div>

    <!-- SUCCESS MESSAGE -->
    @if(session('success'))
        <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- CATEGORY TABLE -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">

        <div class="p-5 border-b border-gray-200 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">

            <div>
                <h3 class="font-semibold text-gray-800">
                    Daftar Kategori
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Kelola kategori pemasukan dan pengeluaran.
                </p>
            </div>

            <a href="{{ route('categories.create') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
                <span>+</span>
                Tambah Kategori
            </a>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full text-sm text-left">

                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Jenis</th>
                        <th class="px-6 py-4">Warna</th>
                        <th class="px-6 py-4">Deskripsi</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse($categories as $category)

                        <tr class="hover:bg-gray-50">

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                        <div
                                            class="category-icon w-9 h-9 rounded-lg flex items-center justify-center text-white"
                                            data-color="{{ $category->color }}"
                                        >
                                            {{ $category->icon ?: '•' }}
                                        </div>

                                    <span class="font-medium text-gray-800">
                                        {{ $category->name }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                @if($category->type === 'income')
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-medium">
                                        Pemasukan
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-red-50 text-red-700 rounded-full text-xs font-medium">
                                        Pengeluaran
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">

                                <span
                                    class="category-color-swatch w-5 h-5 rounded border border-gray-200"
                                    data-color="{{ $category->color }}"
                                ></span>

                                    <span class="text-gray-500 text-xs">
                                        {{ $category->color }}
                                    </span>

                                </div>
                            </td>

                            <td class="px-6 py-4 text-gray-500">
                                {{ $category->description ?: '-' }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($category->is_active)
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-gray-100 text-gray-500 rounded-full text-xs">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex justify-center items-center gap-3">

                                    <a href="{{ route('categories.edit', $category) }}"
                                       class="text-blue-600 hover:text-blue-800 font-medium">
                                        Edit
                                    </a>

                                    <form method="POST"
                                          action="{{ route('categories.destroy', $category) }}"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="text-red-500 hover:text-red-700 font-medium">
                                            Hapus
                                        </button>

                                    </form>

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="text-gray-400 text-4xl mb-3">▤</div>
                                <div class="font-medium text-gray-700">
                                    Belum ada kategori
                                </div>
                                <p class="text-sm text-gray-400 mt-1">
                                    Silakan tambahkan kategori pertama Anda.
                                </p>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-finance-layout>


<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.category-color-swatch').forEach(function (element) {

        const color = element.dataset.color;

        if (/^#[0-9A-Fa-f]{6}$/.test(color)) {
            element.style.setProperty('background-color', color);
        }

    });

});
</script>