
@php
    $isEdit = isset($account);
@endphp

<div class="bg-white rounded-xl border border-gray-200 shadow-sm">

    <div class="p-6 space-y-5">

        <!-- NAMA REKENING -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Nama Rekening <span class="text-red-500">*</span>
            </label>

            <input type="text"
                   name="name"
                   value="{{ old('name', $account->name ?? '') }}"
                   required
                   maxlength="100"
                   placeholder="Contoh: BCA, Cash, DANA"
                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">

            @error('name')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>


        <!-- JENIS REKENING -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Jenis Rekening <span class="text-red-500">*</span>
            </label>

            <select name="type"
                    id="accountType"
                    required
                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">

                <option value="">Pilih jenis rekening</option>

                <option value="cash"
                    @selected(old('type', $account->type ?? '') === 'cash')>
                    Cash / Tunai
                </option>

                <option value="bank"
                    @selected(old('type', $account->type ?? '') === 'bank')>
                    Bank
                </option>

                <option value="ewallet"
                    @selected(old('type', $account->type ?? '') === 'ewallet')>
                    E-Wallet
                </option>

            </select>

            @error('type')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>


        <!-- BANK NAME -->
        <div id="bankFields">

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Bank / Provider
                </label>

                <input type="text"
                       name="bank_name"
                       value="{{ old('bank_name', $account->bank_name ?? '') }}"
                       maxlength="100"
                       placeholder="Contoh: BCA, Mandiri, DANA"
                       class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">

                @error('bank_name')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>


            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor Rekening / Nomor Akun
                </label>

                <input type="text"
                       name="account_number"
                       value="{{ old('account_number', $account->account_number ?? '') }}"
                       maxlength="50"
                       placeholder="Masukkan nomor rekening jika ada"
                       class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">

                @error('account_number')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>


        <!-- SALDO AWAL -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Saldo Awal (Rp) <span class="text-red-500">*</span>
            </label>

            <input type="number"
                   name="opening_balance"
                   value="{{ old('opening_balance', $account->opening_balance ?? '0') }}"
                   required
                   min="0"
                   step="0.01"
                   placeholder="0"
                   class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">

            @error('opening_balance')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror

            <p class="text-xs text-gray-400 mt-2">
                Masukkan saldo yang tersedia saat pertama kali menggunakan aplikasi.
            </p>
        </div>


        <!-- NOTES -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Catatan
            </label>

            <textarea name="notes"
                      rows="3"
                      maxlength="1000"
                      placeholder="Catatan tambahan..."
                      class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('notes', $account->notes ?? '') }}</textarea>

            @error('notes')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>


        @if($isEdit)

            <!-- STATUS -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Status Rekening
                </label>

                <select name="is_active"
                        required
                        class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">

                    <option value="1"
                        @selected((string) old('is_active', (int) $account->is_active) === '1')>
                        Aktif
                    </option>

                    <option value="0"
                        @selected((string) old('is_active', (int) $account->is_active) === '0')>
                        Nonaktif
                    </option>

                </select>

                @error('is_active')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

        @endif

    </div>


    <!-- FORM FOOTER -->
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">

        <a href="{{ route('accounts.index') }}"
           class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-100">
            Batal
        </a>

        <button type="submit"
                class="px-5 py-2.5 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">
            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Rekening' }}
        </button>

    </div>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {

        const typeSelect = document.getElementById('accountType');
        const bankFields = document.getElementById('bankFields');

        function toggleBankFields() {

            const selectedType = typeSelect.value;

            bankFields.style.display =
                selectedType === 'cash' || selectedType === ''
                    ? 'none'
                    : 'block';

        }

        typeSelect.addEventListener('change', toggleBankFields);

        toggleBankFields();

    });
</script>
