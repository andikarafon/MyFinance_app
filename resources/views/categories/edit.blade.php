
<x-finance-layout>
    <x-slot name="title">Edit Kategori</x-slot>

    <div class="max-w-3xl mx-auto">

        <div class="mb-6">
            <a href="{{ route('categories.index') }}"
               class="text-sm text-gray-500 hover:text-emerald-600">
                ← Kembali ke Daftar Kategori
            </a>

            <h2 class="text-xl font-semibold text-gray-800 mt-3">
                Edit Kategori
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Perbarui informasi kategori {{ $category->name }}.
            </p>
        </div>

        @if($errors->any())
            <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="font-medium text-red-700 mb-2">
                    Terdapat kesalahan pada form:
                </div>

                <ul class="list-disc list-inside text-sm text-red-600">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('categories.update', $category) }}">
            @csrf
            @method('PUT')

            @include('categories.form')
        </form>

    </div>

</x-finance-layout>
