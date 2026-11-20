
@php
    $isEdit = isset($category);
@endphp

<div class="bg-white rounded-xl border border-gray-200 shadow-sm">

    <div class="p-6 space-y-5">

        <!-- NAMA -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Nama Kategori <span class="text-red-500">*</span>
            </label>

            <input type="text"
                   name="name"
                   value="{{ old('name', $category->name ?? '') }}"
                   required
                   maxlength="100"
                   placeholder="Contoh: Gaji, Makan, Transportasi"
                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">

            @error('name')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- JENIS -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Jenis Kategori <span class="text-red-500">*</span>
            </label>

            <select name="type"
                    required
                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">

                <option value="">Pilih jenis kategori</option>

                <option value="income"
                    @selected(old('type', $category->type ?? '') === 'income')>
                    Pemasukan
                </option>

                <option value="expense"
                    @selected(old('type', $category->type ?? '') === 'expense')>
                    Pengeluaran
                </option>

            </select>

            @error('type')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>


        <!-- ICON -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Ikon Kategori <span class="text-red-500">*</span>
            </label>

            <select name="icon"
                    id="categoryIcon"
                    required
                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">

                <option value="">Pilih Ikon Kategori</option>

                <optgroup label="Keuangan">
                    <option value="💰" @selected(old('icon', $category->icon ?? '') === '💰')>💰 Uang</option>
                    <option value="💵" @selected(old('icon', $category->icon ?? '') === '💵')>💵 Uang Tunai</option>
                    <option value="🏦" @selected(old('icon', $category->icon ?? '') === '🏦')>🏦 Bank</option>
                    <option value="💳" @selected(old('icon', $category->icon ?? '') === '💳')>💳 Kartu</option>
                    <option value="📈" @selected(old('icon', $category->icon ?? '') === '📈')>📈 Investasi</option>
                    <option value="🎁" @selected(old('icon', $category->icon ?? '') === '🎁')>🎁 Bonus / Hadiah</option>
                </optgroup>

                <optgroup label="Kebutuhan">
                    <option value="🍔" @selected(old('icon', $category->icon ?? '') === '🍔')>🍔 Makanan</option>
                    <option value="🛒" @selected(old('icon', $category->icon ?? '') === '🛒')>🛒 Belanja</option>
                    <option value="🏠" @selected(old('icon', $category->icon ?? '') === '🏠')>🏠 Rumah</option>
                    <option value="🚗" @selected(old('icon', $category->icon ?? '') === '🚗')>🚗 Kendaraan</option>
                    <option value="⛽" @selected(old('icon', $category->icon ?? '') === '⛽')>⛽ Bahan Bakar</option>
                    <option value="💡" @selected(old('icon', $category->icon ?? '') === '💡')>💡 Listrik</option>
                    <option value="📱" @selected(old('icon', $category->icon ?? '') === '📱')>📱 Komunikasi</option>
                    <option value="💊" @selected(old('icon', $category->icon ?? '') === '💊')>💊 Kesehatan</option>
                </optgroup>

                <optgroup label="Aktivitas">
                    <option value="🎓" @selected(old('icon', $category->icon ?? '') === '🎓')>🎓 Pendidikan</option>
                    <option value="✈️" @selected(old('icon', $category->icon ?? '') === '✈️')>✈️ Perjalanan</option>
                    <option value="🎮" @selected(old('icon', $category->icon ?? '') === '🎮')>🎮 Hiburan</option>
                    <option value="⚽" @selected(old('icon', $category->icon ?? '') === '⚽')>⚽ Olahraga</option>
                    <option value="❤️" @selected(old('icon', $category->icon ?? '') === '❤️')>❤️ Kesehatan</option>
                    <option value="🐾" @selected(old('icon', $category->icon ?? '') === '🐾')>🐾 Hewan Peliharaan</option>
                </optgroup>

                <optgroup label="Lainnya">
                    <option value="💼" @selected(old('icon', $category->icon ?? '') === '💼')>💼 Pekerjaan</option>
                    <option value="📦" @selected(old('icon', $category->icon ?? '') === '📦')>📦 Lain-lain</option>
                    <option value="🔧" @selected(old('icon', $category->icon ?? '') === '🔧')>🔧 Perawatan</option>
                    <option value="📄" @selected(old('icon', $category->icon ?? '') === '📄')>📄 Administrasi</option>
                </optgroup>

            </select>

            @error('icon')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror

            <p class="text-xs text-gray-400 mt-2">
                Pilih ikon yang paling sesuai dengan kategori keuangan.
            </p>
        </div>


        <!-- COLOR -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Warna Kategori <span class="text-red-500">*</span>
            </label>

            <div class="flex items-center gap-3">
                <input type="color"
                       id="categoryColor"
                       name="color"
                       value="{{ old('color', $category->color ?? '#64748b') }}"
                       required
                       class="w-14 h-11 border border-gray-300 rounded-lg cursor-pointer">

                <span id="colorValue" class="text-sm text-gray-500">
                    {{ old('color', $category->color ?? '#64748b') }}
                </span>
            </div>

            @error('color')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- DESCRIPTION -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Deskripsi
            </label>

            <textarea name="description"
                      rows="3"
                      maxlength="1000"
                      placeholder="Deskripsi kategori..."
                      class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('description', $category->description ?? '') }}</textarea>

            @error('description')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- STATUS -->
        @if($isEdit)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Status
                </label>

                <select name="is_active"
                        required
                        class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">

                    <option value="1"
                        @selected((string) old('is_active', (int) $category->is_active) === '1')>
                        Aktif
                    </option>

                    <option value="0"
                        @selected((string) old('is_active', (int) $category->is_active) === '0')>
                        Nonaktif
                    </option>

                </select>

                @error('is_active')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        @endif

    </div>

    <!-- FOOTER -->
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">

        <a href="{{ route('categories.index') }}"
           class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-100">
            Batal
        </a>

        <button type="submit"
                class="px-5 py-2.5 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">
            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Kategori' }}
        </button>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const colorInput = document.getElementById('categoryColor');
        const colorValue = document.getElementById('colorValue');

        colorInput.addEventListener('input', function () {
            colorValue.textContent = this.value;
        });
    });
</script>
