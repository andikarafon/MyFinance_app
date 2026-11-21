<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    private function validateType(string $type): void
    {
        abort_unless(
            in_array($type, ['income', 'expense', 'transfer'], true),
            404
        );
    }

    private function getPageData(string $type): array
    {
        $this->validateType($type);

        $userId = Auth::id();

        $accounts = Account::where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = Category::where('user_id', $userId)
            ->where('type', $type === 'income' ? 'income' : 'expense')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return compact('accounts', 'categories', 'type');
    }

    /**
     * Daftar transaksi berdasarkan jenis.
     */
    public function index(Request $request, string $type)
    {
        $this->validateType($type);

        $query = Transaction::with([
                'account',
                'destinationAccount',
                'category',
            ])
            ->where('user_id', Auth::id())
            ->where('type', $type);

        if ($request->filled('start_date')) {
            $query->whereDate(
                'transaction_date',
                '>=',
                $request->start_date
            );
        }

        if ($request->filled('end_date')) {
            $query->whereDate(
                'transaction_date',
                '<=',
                $request->end_date
            );
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($category) use ($search) {
                        $category->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('account', function ($account) use ($search) {
                        $account->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('destinationAccount', function ($account) use ($search) {
                        $account->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $transactions = $query
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $total = Transaction::where('user_id', Auth::id())
            ->where('type', $type)
            ->when($request->filled('start_date'), function ($q) use ($request) {
                $q->whereDate('transaction_date', '>=', $request->start_date);
            })
            ->when($request->filled('end_date'), function ($q) use ($request) {
                $q->whereDate('transaction_date', '<=', $request->end_date);
            })
            ->sum('amount');

        return view('transactions.index', array_merge(
            $this->getPageData($type),
            compact('transactions', 'total')
        ));
    }

    /**
     * Form tambah transaksi.
     */
    public function create(string $type)
    {
        $data = $this->getPageData($type);

        abort_if(
            $data['accounts']->isEmpty(),
            403,
            'Silakan buat rekening aktif terlebih dahulu.'
        );

        if ($type !== 'transfer' && $data['categories']->isEmpty()) {
            abort(403, 'Silakan buat kategori aktif terlebih dahulu.');
        }

        return view('transactions.create', $data);
    }

    /**
     * Simpan transaksi baru.
     */
    public function store(Request $request, string $type)
    {
        $this->validateType($type);

        $userId = Auth::id();

        $rules = [
            'account_id' => [
                'required',
                'integer',
                Rule::exists('accounts', 'id')->where(function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                        ->where('is_active', true);
                }),
            ],
            'amount' => ['required', 'numeric', 'gt:0'],
            'transaction_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];

        if ($type === 'transfer') {
            $rules['destination_account_id'] = [
                'required',
                'integer',
                'different:account_id',
                Rule::exists('accounts', 'id')->where(function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                        ->where('is_active', true);
                }),
            ];
        } else {
            $rules['category_id'] = [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where(function ($q) use ($userId, $type) {
                    $q->where('user_id', $userId)
                        ->where('type', $type)
                        ->where('is_active', true);
                }),
            ];
        }

        $validated = $request->validate($rules);

        $validated['user_id'] = $userId;
        $validated['type'] = $type;

        if ($type === 'transfer') {
            $validated['category_id'] = null;
        } else {
            $validated['destination_account_id'] = null;
        }

        Transaction::create($validated);

        return redirect()
            ->route('transactions.index', $type)
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    /**
     * Form edit transaksi.
     */
    public function edit(string $type, Transaction $transaction)
    {
        $this->authorizeTransaction($transaction, $type);

        $data = $this->getPageData($type);

        return view('transactions.edit', array_merge(
            $data,
            compact('transaction')
        ));
    }

    /**
     * Update transaksi.
     */
    public function update(
        Request $request,
        string $type,
        Transaction $transaction
    ) {
        $this->authorizeTransaction($transaction, $type);

        $userId = Auth::id();

        $rules = [
            'account_id' => [
                'required',
                'integer',
                Rule::exists('accounts', 'id')
                    ->where(fn ($q) => $q->where('user_id', $userId)),
            ],
            'amount' => ['required', 'numeric', 'gt:0'],
            'transaction_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];

        if ($type === 'transfer') {
            $rules['destination_account_id'] = [
                'required',
                'integer',
                'different:account_id',
                Rule::exists('accounts', 'id')
                    ->where(fn ($q) => $q->where('user_id', $userId)),
            ];
        } else {
            $rules['category_id'] = [
                'required',
                'integer',
                Rule::exists('categories', 'id')
                    ->where(fn ($q) => $q->where('user_id', $userId)
                        ->where('type', $type)),
            ];
        }

        $validated = $request->validate($rules);

        if ($type === 'transfer') {
            $validated['category_id'] = null;
        } else {
            $validated['destination_account_id'] = null;
        }

        $transaction->update($validated);

        return redirect()
            ->route('transactions.index', $type)
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Hapus transaksi.
     */
    public function destroy(string $type, Transaction $transaction)
    {
        $this->authorizeTransaction($transaction, $type);

        $transaction->delete();

        return redirect()
            ->route('transactions.index', $type)
            ->with('success', 'Transaksi berhasil dihapus.');
    }

    /**
     * Validasi kepemilikan transaksi.
     */
    private function authorizeTransaction(
        Transaction $transaction,
        string $type
    ): void {
        $this->validateType($type);

        abort_unless(
            $transaction->user_id === Auth::id()
            && $transaction->type === $type,
            404
        );
    }
}
