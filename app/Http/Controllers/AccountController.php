<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    /**
     * Menampilkan daftar rekening.
     */
    public function index()
    {
        $accounts = Account::where('user_id', Auth::id())
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->get();

        $totalBalance = $accounts
            ->where('is_active', true)
            ->sum('opening_balance');

        return view('accounts.index', compact(
            'accounts',
            'totalBalance'
        ));
    }

    /**
     * Menampilkan form tambah rekening.
     */
    public function create()
    {
        return view('accounts.create');
    }

    /**
     * Menyimpan rekening baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::in([
                'cash',
                'bank',
                'ewallet',
            ])],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'opening_balance' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_active'] = true;

        Account::create($validated);

        return redirect()
            ->route('accounts.index')
            ->with('success', 'Rekening berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit rekening.
     */
    public function edit(Account $account)
    {
        $this->authorizeAccount($account);

        return view('accounts.edit', compact('account'));
    }

    /**
     * Memperbarui rekening.
     */
    public function update(Request $request, Account $account)
    {
        $this->authorizeAccount($account);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::in([
                'cash',
                'bank',
                'ewallet',
            ])],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'opening_balance' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['required', 'boolean'],
        ]);

        $account->update($validated);

        return redirect()
            ->route('accounts.index')
            ->with('success', 'Rekening berhasil diperbarui.');
    }

    /**
     * Menghapus rekening.
     */
    public function destroy(Account $account)
    {
        $this->authorizeAccount($account);

        // Nantinya rekening yang sudah memiliki transaksi
        // tidak boleh dihapus agar riwayat keuangan terjaga.

        $account->delete();

        return redirect()
            ->route('accounts.index')
            ->with('success', 'Rekening berhasil dihapus.');
    }

    /**
     * Memastikan rekening milik pengguna yang login.
     */
    private function authorizeAccount(Account $account): void
    {
        abort_unless(
            $account->user_id === Auth::id(),
            403,
            'Anda tidak memiliki akses ke rekening ini.'
        );
    }
}
