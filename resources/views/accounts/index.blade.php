
<x-finance-layout>
    <x-slot name="title">Master Rekening</x-slot>

    <!-- SUMMARY -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="text-sm text-gray-500">Total Saldo Awal</div>
            <div class="text-2xl font-bold text-gray-800 mt-2">
                Rp {{ number_format($totalBalance, 0, ',', '.') }}
            </div>
            <div class="text-xs text-gray-400 mt-2">
                Total rekening aktif
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="text-sm text-gray-500">Jumlah Rekening</div>
            <div class="text-2xl font-bold text-emerald-600 mt-2">
                {{ $accounts->where('is_active', true)->count() }}
            </div>
            <div class="text-xs text-gray-400 mt-2">
                Rekening aktif
            </div>
        </div>

    </div>


    <!-- ALERT SUCCESS -->
    @if(session('success'))
        <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif


    <!-- ACCOUNT TABLE -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">

        <div class="p-5 border-b border-gray-200 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">

            <div>
                <h3 class="font-semibold text-gray-800">
                    Daftar Rekening
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Kelola rekening dan saldo awal Anda.
                </p>
            </div>

            <a href="{{ route('accounts.create') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">

                <span>+</span>
                Tambah Rekening

            </a>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-sm text-left">

                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">

                    <tr>
                        <th class="px-6 py-4">Rekening</th>
                        <th class="px-6 py-4">Jenis</th>
                        <th class="px-6 py-4">Nomor / Bank</th>
                        <th class="px-6 py-4 text-right">Saldo Awal</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-100">

                    @forelse($accounts as $account)

                        <tr class="hover:bg-gray-50">

                            <!-- NAME -->
                            <td class="px-6 py-4">

                                <div class="font-medium text-gray-800">
                                    {{ $account->name }}
                                </div>

                                @if($account->notes)
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ $account->notes }}
                                    </div>
                                @endif

                            </td>


                            <!-- TYPE -->
                            <td class="px-6 py-4">

                                @if($account->type === 'cash')
                                    <span class="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full text-xs font-medium">
                                        Cash
                                    </span>
                                @elseif($account->type === 'bank')
                                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                                        Bank
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-purple-50 text-purple-700 rounded-full text-xs font-medium">
                                        E-Wallet
                                    </span>
                                @endif

                            </td>


                            <!-- BANK INFORMATION -->
                            <td class="px-6 py-4 text-gray-500">

                                @if($account->bank_name)
                                    <div>{{ $account->bank_name }}</div>
                                @endif

                                @if($account->account_number)
                                    <div class="text-xs mt-1">
                                        {{ $account->account_number }}
                                    </div>
                                @endif

                                @if(!$account->bank_name && !$account->account_number)
                                    -
                                @endif

                            </td>


                            <!-- BALANCE -->
                            <td class="px-6 py-4 text-right font-semibold text-gray-800">
                                Rp {{ number_format((float) $account->opening_balance, 0, ',', '.') }}
                            </td>


                            <!-- STATUS -->
                            <td class="px-6 py-4 text-center">

                                @if($account->is_active)
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-gray-100 text-gray-500 rounded-full text-xs">
                                        Nonaktif
                                    </span>
                                @endif

                            </td>


                            <!-- ACTION -->
                            <td class="px-6 py-4">

                                <div class="flex justify-center items-center gap-3">

                                    <a href="{{ route('accounts.edit', $account) }}"
                                       class="text-blue-600 hover:text-blue-800 font-medium">
                                        Edit
                                    </a>

                                    <form method="POST"
                                          action="{{ route('accounts.destroy', $account) }}"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus rekening ini?')">

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

                                <div class="text-gray-400 text-4xl mb-3">
                                    ▣
                                </div>

                                <div class="font-medium text-gray-700">
                                    Belum ada rekening
                                </div>

                                <p class="text-sm text-gray-400 mt-1">
                                    Silakan tambahkan rekening pertama Anda.
                                </p>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-finance-layout>
