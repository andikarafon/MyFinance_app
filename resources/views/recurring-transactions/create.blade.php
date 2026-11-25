<x-finance-layout title="Tambah Transaksi Berulang">

    <div class="mx-auto max-w-5xl space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-xl font-bold text-slate-800">
                    Tambah Jadwal Transaksi
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Buat jadwal pemasukan atau pengeluaran secara berkala.
                </p>
            </div>

            <a href="{{ route('recurring-transactions.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50">
                Kembali
            </a>

        </div>


        {{-- VALIDATION ERRORS --}}
        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="mb-2 font-semibold">
                    Terdapat kesalahan pada pengisian form:
                </p>

                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <form method="POST"
              action="{{ route('recurring-transactions.store') }}"
              class="space-y-6">

            @csrf


            {{-- =====================================================
                 1. INFORMASI TRANSAKSI
            ====================================================== --}}
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-6 py-4">
                    <h3 class="font-semibold text-slate-800">
                        1. Informasi Transaksi
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Tentukan jenis transaksi dan nominal yang akan dijadwalkan.
                    </p>
                </div>


                <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">

                    {{-- NAMA JADWAL --}}
                    <div class="md:col-span-2">

                        <label for="name"
                               class="mb-1 block text-sm font-medium text-slate-700">
                            Nama Jadwal
                            <span class="text-red-500">*</span>
                        </label>

                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name') }}"
                               maxlength="150"
                               required
                               placeholder="Contoh: Gaji Bulanan, Bayar Listrik, Cicilan Rumah"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">

                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                    </div>


                    {{-- JENIS TRANSAKSI --}}
                    <div>

                        <label for="type"
                               class="mb-1 block text-sm font-medium text-slate-700">
                            Jenis Transaksi
                            <span class="text-red-500">*</span>
                        </label>

                        <select name="type"
                                id="type"
                                required
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">

                            <option value="">Pilih Jenis Transaksi</option>

                            <option value="income"
                                {{ old('type') === 'income' ? 'selected' : '' }}>
                                Pemasukan
                            </option>

                            <option value="expense"
                                {{ old('type') === 'expense' ? 'selected' : '' }}>
                                Pengeluaran
                            </option>

                        </select>

                        @error('type')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                    </div>


                    {{-- KATEGORI --}}
                    <div>

                        <label for="category_id"
                               class="mb-1 block text-sm font-medium text-slate-700">
                            Kategori
                            <span class="text-red-500">*</span>
                        </label>

                        <select name="category_id"
                                id="category_id"
                                required
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">

                            <option value="">Pilih Kategori</option>

                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                        data-type="{{ $category->type }}"
                                    {{ (string) old('category_id') === (string) $category->id ? 'selected' : '' }}>

                                    {{ $category->name }}

                                </option>
                            @endforeach

                        </select>

                        @error('category_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                    </div>


                    {{-- REKENING --}}
                    <div>

                        <label for="account_id"
                               class="mb-1 block text-sm font-medium text-slate-700">
                            Rekening
                            <span class="text-red-500">*</span>
                        </label>

                        <select name="account_id"
                                id="account_id"
                                required
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">

                            <option value="">Pilih Rekening</option>

                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}"
                                    {{ (string) old('account_id') === (string) $account->id ? 'selected' : '' }}>

                                    {{ $account->name }}

                                </option>
                            @endforeach

                        </select>

                        @error('account_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                    </div>


                    {{-- NOMINAL --}}
                    <div>

                        <label for="amount"
                               class="mb-1 block text-sm font-medium text-slate-700">
                            Nominal Transaksi (Rp)
                            <span class="text-red-500">*</span>
                        </label>

                        <input type="number"
                               name="amount"
                               id="amount"
                               value="{{ old('amount') }}"
                               min="0.01"
                               max="9999999999999999"
                               step="0.01"
                               required
                               placeholder="Contoh: 5000000"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">

                        <p class="mt-1 text-xs text-slate-400">
                            Nominal ini menjadi nominal dasar setiap jadwal.
                        </p>

                        @error('amount')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 2. PENGATURAN FREKUENSI
            ====================================================== --}}
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-6 py-4">
                    <h3 class="font-semibold text-slate-800">
                        2. Pengaturan Frekuensi
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Tentukan seberapa sering transaksi akan dijadwalkan.
                    </p>
                </div>


                <div class="space-y-5 p-6">

                    {{-- FREKUENSI --}}
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                        <div>

                            <label for="frequency"
                                   class="mb-1 block text-sm font-medium text-slate-700">
                                Frekuensi
                                <span class="text-red-500">*</span>
                            </label>

                            <select name="frequency"
                                    id="frequency"
                                    required
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">

                                <option value="">Pilih Frekuensi</option>

                                <option value="daily"
                                    {{ old('frequency') === 'daily' ? 'selected' : '' }}>
                                    Harian
                                </option>

                                <option value="weekly"
                                    {{ old('frequency') === 'weekly' ? 'selected' : '' }}>
                                    Mingguan
                                </option>

                                <option value="monthly"
                                    {{ old('frequency') === 'monthly' ? 'selected' : '' }}>
                                    Bulanan
                                </option>

                                <option value="yearly"
                                    {{ old('frequency') === 'yearly' ? 'selected' : '' }}>
                                    Tahunan
                                </option>

                            </select>

                            @error('frequency')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror

                        </div>


                        {{-- INTERVAL --}}
                        <div>

                            <label for="interval"
                                   class="mb-1 block text-sm font-medium text-slate-700">
                                Setiap Berapa
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="number"
                                   name="interval"
                                   id="interval"
                                   value="{{ old('interval', 1) }}"
                                   min="1"
                                   max="9999"
                                   required
                                   class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">

                            <p id="interval-help"
                               class="mt-1 text-xs text-slate-400">
                                Contoh: 1 berarti setiap satu periode.
                            </p>

                            @error('interval')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror

                        </div>

                    </div>


                    {{-- HARI DALAM MINGGU --}}
                    <div id="weekly-fields" class="hidden">

                        <label for="day_of_week"
                               class="mb-1 block text-sm font-medium text-slate-700">
                            Hari
                            <span class="text-red-500">*</span>
                        </label>

                        <select name="day_of_week"
                                id="day_of_week"
                                data-required="true"
                                disabled
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">

                            <option value="">Pilih Hari</option>

                            <option value="1" {{ old('day_of_week') == '1' ? 'selected' : '' }}>Senin</option>
                            <option value="2" {{ old('day_of_week') == '2' ? 'selected' : '' }}>Selasa</option>
                            <option value="3" {{ old('day_of_week') == '3' ? 'selected' : '' }}>Rabu</option>
                            <option value="4" {{ old('day_of_week') == '4' ? 'selected' : '' }}>Kamis</option>
                            <option value="5" {{ old('day_of_week') == '5' ? 'selected' : '' }}>Jumat</option>
                            <option value="6" {{ old('day_of_week') == '6' ? 'selected' : '' }}>Sabtu</option>
                            <option value="7" {{ old('day_of_week') == '7' ? 'selected' : '' }}>Minggu</option>

                        </select>

                        @error('day_of_week')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                    </div>


                    {{-- HARI DALAM BULAN --}}
                    <div id="monthly-fields" class="hidden">

                        <label for="day_of_month"
                               class="mb-1 block text-sm font-medium text-slate-700">
                            Tanggal dalam Bulan
                            <span class="text-red-500">*</span>
                        </label>

                        <input type="number"
                               name="day_of_month"
                               id="day_of_month"
                               value="{{ old('day_of_month', 1) }}"
                               min="1"
                               max="31"
                               data-required="true"
                               disabled
                               placeholder="1 sampai 31"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">

                        <p class="mt-1 text-xs text-slate-400">
                            Jika tanggal tidak tersedia pada suatu bulan, sistem menggunakan hari terakhir bulan tersebut.
                        </p>

                        @error('day_of_month')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                    </div>


                    {{-- BULAN DALAM TAHUN --}}
                    <div id="yearly-fields" class="hidden">

                        <label for="month_of_year"
                               class="mb-1 block text-sm font-medium text-slate-700">
                            Bulan
                            <span class="text-red-500">*</span>
                        </label>

                        <select name="month_of_year"
                                id="month_of_year"
                                data-required="true"
                                disabled
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">

                            <option value="">Pilih Bulan</option>

                            @foreach ([
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
                                12 => 'Desember'
                            ] as $monthNumber => $monthName)

                                <option value="{{ $monthNumber }}"
                                    {{ (string) old('month_of_year') === (string) $monthNumber ? 'selected' : '' }}>
                                    {{ $monthName }}
                                </option>

                            @endforeach

                        </select>

                        @error('month_of_year')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 3. PERIODE JADWAL
            ====================================================== --}}
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-6 py-4">
                    <h3 class="font-semibold text-slate-800">
                        3. Periode Jadwal
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Tentukan masa berlaku jadwal transaksi.
                    </p>
                </div>


                <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">

                    {{-- TANGGAL MULAI --}}
                    <div>

                        <label for="start_date"
                               class="mb-1 block text-sm font-medium text-slate-700">
                            Tanggal Mulai
                            <span class="text-red-500">*</span>
                        </label>

                        <input type="date"
                               name="start_date"
                               id="start_date"
                               value="{{ old('start_date', now()->format('Y-m-d')) }}"
                               required
                               class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">

                        @error('start_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                    </div>


                    {{-- TANGGAL BERAKHIR --}}
                    <div>

                        <label for="end_date"
                               class="mb-1 block text-sm font-medium text-slate-700">
                            Tanggal Berakhir
                        </label>

                        <input type="date"
                               name="end_date"
                               id="end_date"
                               value="{{ old('end_date') }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">

                        <p class="mt-1 text-xs text-slate-400">
                            Kosongkan jika jadwal tidak memiliki batas akhir.
                        </p>

                        @error('end_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 4. PENGATURAN EKSEKUSI
            ====================================================== --}}
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-6 py-4">
                    <h3 class="font-semibold text-slate-800">
                        4. Pengaturan Eksekusi
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Tentukan bagaimana jadwal diproses oleh sistem.
                    </p>
                </div>


                <div class="space-y-5 p-6">

                    {{-- MODE EKSEKUSI --}}
                    <div>

                        <label for="confirmation_required"
                               class="mb-1 block text-sm font-medium text-slate-700">
                            Mode Eksekusi
                            <span class="text-red-500">*</span>
                        </label>

                        <select name="confirmation_required"
                                id="confirmation_required"
                                required
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">

                            <option value="0"
                                {{ old('confirmation_required', '0') == '0' ? 'selected' : '' }}>
                                Otomatis — langsung membuat transaksi
                            </option>

                            <option value="1"
                                {{ old('confirmation_required') == '1' ? 'selected' : '' }}>
                                Perlu Konfirmasi — menunggu persetujuan
                            </option>

                        </select>

                        <div class="mt-2 rounded-lg bg-blue-50 p-3 text-xs text-blue-700">
                            <strong>Otomatis:</strong> sistem membuat transaksi ketika jadwal jatuh tempo.

                            <br>

                            <strong>Perlu Konfirmasi:</strong> sistem membuat pengingat dan menunggu konfirmasi sebelum transaksi dicatat.
                        </div>

                        @error('confirmation_required')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                    </div>


                    {{-- PENGINGAT --}}
                    <div>

                        <label for="reminder_days"
                               class="mb-1 block text-sm font-medium text-slate-700">
                            Pengingat Sebelum Jadwal (Hari)
                            <span class="text-red-500">*</span>
                        </label>

                        <input type="number"
                               name="reminder_days"
                               id="reminder_days"
                               value="{{ old('reminder_days', 1) }}"
                               min="0"
                               max="365"
                               required
                               class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">

                        <p class="mt-1 text-xs text-slate-400">
                            Isi 0 jika tidak membutuhkan pengingat. Pengingat ditampilkan melalui Dashboard.
                        </p>

                        @error('reminder_days')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                    </div>

                </div>

            </div>


            {{-- ACTION BUTTON --}}
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                <a href="{{ route('recurring-transactions.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">
                    Batal
                </a>

                <button type="submit"
                        style="background-color: #059669; color: #ffffff;"
                        class="inline-flex items-center justify-center rounded-lg px-5 py-2.5 text-sm font-semibold shadow-sm transition hover:bg-emerald-700">
                    Simpan Jadwal
                </button>

            </div>

        </form>

    </div>


    {{-- =====================================================
         DYNAMIC FORM FIELDS
    ====================================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const frequency = document.getElementById('frequency');

            const weeklyFields = document.getElementById('weekly-fields');
            const monthlyFields = document.getElementById('monthly-fields');
            const yearlyFields = document.getElementById('yearly-fields');

            const intervalHelp = document.getElementById('interval-help');

            const conditionalFields = [
                weeklyFields,
                monthlyFields,
                yearlyFields
            ];

            function updateFrequencyFields() {

                const selectedFrequency = frequency.value;

                conditionalFields.forEach(function (container) {

                    container.classList.add('hidden');

                    container.querySelectorAll('input, select').forEach(function (field) {
                        field.disabled = true;
                        field.required = false;
                    });

                });


                if (selectedFrequency === 'weekly') {

                    weeklyFields.classList.remove('hidden');

                    weeklyFields.querySelectorAll('input, select').forEach(function (field) {
                        field.disabled = false;
                        field.required = true;
                    });

                    intervalHelp.textContent =
                        'Contoh: 1 berarti setiap minggu, 2 berarti setiap dua minggu.';

                } else if (selectedFrequency === 'monthly') {

                    monthlyFields.classList.remove('hidden');

                    monthlyFields.querySelectorAll('input, select').forEach(function (field) {
                        field.disabled = false;
                        field.required = true;
                    });

                    intervalHelp.textContent =
                        'Contoh: 1 berarti setiap bulan, 2 berarti setiap dua bulan.';

                } else if (selectedFrequency === 'yearly') {

                    monthlyFields.classList.remove('hidden');
                    yearlyFields.classList.remove('hidden');

                    monthlyFields.querySelectorAll('input, select').forEach(function (field) {
                        field.disabled = false;
                        field.required = true;
                    });

                    yearlyFields.querySelectorAll('input, select').forEach(function (field) {
                        field.disabled = false;
                        field.required = true;
                    });

                    intervalHelp.textContent =
                        'Contoh: 1 berarti setiap tahun, 2 berarti setiap dua tahun.';

                } else {

                    intervalHelp.textContent =
                        'Contoh: 1 berarti setiap hari, 2 berarti setiap dua hari.';

                }

            }


            // FILTER KATEGORI SESUAI JENIS TRANSAKSI
            const typeSelect = document.getElementById('type');
            const categorySelect = document.getElementById('category_id');

            const categoryOptions = Array.from(categorySelect.options);

            function updateCategories() {

                const selectedType = typeSelect.value;
                const currentCategory = categorySelect.value;

                categorySelect.innerHTML = '';

                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = 'Pilih Kategori';

                categorySelect.appendChild(placeholder);

                categoryOptions.forEach(function (option) {

                    if (!option.value) {
                        return;
                    }

                    if (option.dataset.type === selectedType) {

                        const newOption = option.cloneNode(true);

                        categorySelect.appendChild(newOption);

                    }

                });

                if (
                    currentCategory &&
                    Array.from(categorySelect.options).some(function (option) {
                        return option.value === currentCategory;
                    })
                ) {
                    categorySelect.value = currentCategory;
                } else {
                    categorySelect.value = '';
                }

            }


            frequency.addEventListener('change', updateFrequencyFields);
            typeSelect.addEventListener('change', updateCategories);

            updateFrequencyFields();
            updateCategories();

        });
    </script>

</x-finance-layout>
