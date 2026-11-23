<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    /**
     * Menampilkan daftar transaksi.
     */
    public function index(Request $request, string $type)
    {
        $this->validateType($type);

        $userId = Auth::id();

        $query = Transaction::query()
            ->where('user_id', $userId)
            ->where('type', $type)
            ->with([
                'account',
                'destinationAccount',
                'category',
            ]);

        // Filter pencarian.
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('account', function ($accountQuery) use ($search) {
                        $accountQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('destinationAccount', function ($accountQuery) use ($search) {
                        $accountQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter tanggal awal.
        if ($request->filled('start_date')) {
            $query->whereDate(
                'transaction_date',
                '>=',
                $request->start_date
            );
        }

        // Filter tanggal akhir.
        if ($request->filled('end_date')) {
            $query->whereDate(
                'transaction_date',
                '<=',
                $request->end_date
            );
        }

        $transactions = $query
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $data = $this->getPageData($type);

        return view('transactions.index', array_merge($data, [
            'type' => $type,
            'transactions' => $transactions,
            'total' => $transactions->total(),
        ]));
    }

    /**
     * Menampilkan form tambah transaksi.
     */
    public function create(string $type)
    {
        $this->validateType($type);

        $data = $this->getPageData($type);

        // Pemasukan dan pengeluaran membutuhkan kategori aktif.
        if ($type !== 'transfer' && $data['categories']->isEmpty()) {
            abort(403, 'SILAKAN BUAT KATEGORI AKTIF TERLEBIH DAHULU.');
        }

        if ($data['accounts']->isEmpty()) {
            abort(403, 'SILAKAN BUAT REKENING AKTIF TERLEBIH DAHULU.');
        }

        return view('transactions.create', array_merge($data, [
            'type' => $type,
        ]));
    }

    /**
     * Menyimpan transaksi baru.
     */
    public function store(Request $request, string $type)
    {
        $this->validateType($type);

        $validated = $this->validateTransaction($request, $type);

        $userId = Auth::id();

        $transactionData = [
            'user_id' => $userId,
            'account_id' => $validated['account_id'],
            'destination_account_id' => $type === 'transfer'
                ? $validated['destination_account_id']
                : null,
            'category_id' => $type !== 'transfer'
                ? $validated['category_id']
                : null,
            'type' => $type,
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'description' => $validated['description'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ];

        DB::transaction(function () use ($transactionData) {
            Transaction::create($transactionData);
        });

        return redirect()
            ->route($this->getIndexRoute($type))
            ->with('success', 'Transaksi berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit transaksi.
     */
    public function edit(string $type, Transaction $transaction)
    {
        $this->validateType($type);

        $this->authorizeTransaction($transaction, $type);

        $data = $this->getPageData($type);

        return view('transactions.edit', array_merge($data, [
            'type' => $type,
            'transaction' => $transaction,
        ]));
    }

    /**
     * Memperbarui transaksi.
     */
    public function update(
        Request $request,
        string $type,
        Transaction $transaction
    ) {
        $this->validateType($type);

        $this->authorizeTransaction($transaction, $type);

        $validated = $this->validateTransaction($request, $type);

        $transactionData = [
            'account_id' => $validated['account_id'],
            'destination_account_id' => $type === 'transfer'
                ? $validated['destination_account_id']
                : null,
            'category_id' => $type !== 'transfer'
                ? $validated['category_id']
                : null,
            'type' => $type,
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'description' => $validated['description'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ];

        DB::transaction(function () use ($transaction, $transactionData) {
            $transaction->update($transactionData);
        });

        return redirect()
            ->route($this->getIndexRoute($type))
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Menghapus transaksi.
     */
    public function destroy(string $type, Transaction $transaction)
    {
        $this->validateType($type);

        $this->authorizeTransaction($transaction, $type);

        DB::transaction(function () use ($transaction) {
            $transaction->delete();
        });

        return redirect()
            ->route($this->getIndexRoute($type))
            ->with('success', 'Transaksi berhasil dihapus.');
    }

    /**
     * Validasi data transaksi.
     */
    private function validateTransaction(Request $request, string $type): array
    {
        $userId = Auth::id();

        $rules = [
            'transaction_date' => [
                'required',
                'date',
            ],

            'account_id' => [
                'required',
                Rule::exists('accounts', 'id')->where(function ($query) use ($userId) {
                    $query->where('user_id', $userId)
                        ->where('is_active', true);
                }),
            ],

            'amount' => [
                'required',
                'numeric',
                'gt:0',
                'max:9999999999999999',
            ],

            'description' => [
                'nullable',
                'string',
                'max:255',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ];

        if ($type === 'transfer') {
            $rules['destination_account_id'] = [
                'required',
                'different:account_id',
                Rule::exists('accounts', 'id')->where(function ($query) use ($userId) {
                    $query->where('user_id', $userId)
                        ->where('is_active', true);
                }),
            ];
        } else {
            $rules['category_id'] = [
                'required',
                Rule::exists('categories', 'id')->where(function ($query) use ($userId, $type) {
                    $query->where('user_id', $userId)
                        ->where('type', $type)
                        ->where('is_active', true);
                }),
            ];
        }

        return $request->validate($rules, [
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'account_id.required' => 'Rekening wajib dipilih.',
            'account_id.exists' => 'Rekening tidak valid atau tidak aktif.',
            'destination_account_id.required' => 'Rekening tujuan wajib dipilih.',
            'destination_account_id.exists' => 'Rekening tujuan tidak valid atau tidak aktif.',
            'destination_account_id.different' => 'Rekening asal dan tujuan tidak boleh sama.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori tidak valid atau tidak aktif.',
            'amount.required' => 'Nominal transaksi wajib diisi.',
            'amount.numeric' => 'Nominal harus berupa angka.',
            'amount.gt' => 'Nominal harus lebih besar dari nol.',
            'description.max' => 'Deskripsi maksimal 255 karakter.',
        ]);
    }

    /**
     * Mengambil rekening dan kategori aktif milik user.
     */
    private function getPageData(string $type): array
    {
        $userId = Auth::id();

        $accounts = Account::query()
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = collect();

        if ($type !== 'transfer') {
            $categories = Category::query()
                ->where('user_id', $userId)
                ->where('type', $type)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        return [
            'accounts' => $accounts,
            'categories' => $categories,
        ];
    }

    /**
     * Memastikan jenis transaksi valid.
     */
    private function validateType(string $type): void
    {
        abort_unless(
            in_array($type, ['income', 'expense', 'transfer'], true),
            404,
            'Jenis transaksi tidak ditemukan.'
        );
    }

    /**
     * Memastikan transaksi milik user dan jenisnya sesuai.
     */
    private function authorizeTransaction(
        Transaction $transaction,
        string $type
    ): void {
        abort_unless(
            $transaction->user_id === Auth::id()
            && $transaction->type === $type,
            403,
            'Anda tidak memiliki akses ke transaksi ini.'
        );
    }

    /**
     * Mendapatkan nama route daftar transaksi.
     */
    private function getIndexRoute(string $type): string
    {
        return match ($type) {
            'income' => 'transactions.income',
            'expense' => 'transactions.expense',
            'transfer' => 'transactions.transfer',
        };
    }
}
