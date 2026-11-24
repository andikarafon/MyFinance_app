<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Account;
use App\Models\Transaction;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BudgetController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {

    // DASHBOARD
    Route::get('/dashboard', function () {

        $userId = Auth::id();

        $today = Carbon::today();

        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        // REKENING AKTIF
        $accounts = Account::query()
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // TOTAL SALDO SELURUH REKENING
        $totalBalance = $accounts->sum(function ($account) {
            return $account->current_balance;
        });

        // JUMLAH REKENING AKTIF
        $totalAccounts = $accounts->count();

        // TOTAL PEMASUKAN BULAN INI
        $totalIncome = Transaction::query()
            ->where('user_id', $userId)
            ->where('type', 'income')
            ->whereBetween('transaction_date', [
                $startOfMonth,
                $endOfMonth,
            ])
            ->sum('amount');

        // TOTAL PENGELUARAN BULAN INI
        $totalExpense = Transaction::query()
            ->where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [
                $startOfMonth,
                $endOfMonth,
            ])
            ->sum('amount');

        // DATA TRANSAKSI ENAM BULAN TERAKHIR
        $chartStartDate = $today->copy()
            ->startOfMonth()
            ->subMonths(5);

        $monthlyTransactions = Transaction::query()
            ->where('user_id', $userId)
            ->whereIn('type', ['income', 'expense'])
            ->whereBetween('transaction_date', [
                $chartStartDate,
                $endOfMonth,
            ])
            ->get(['type', 'amount', 'transaction_date']);

        $chartData = collect();

        for ($i = 0; $i < 6; $i++) {

            $month = $chartStartDate->copy()->addMonths($i);

            $monthTransactions = $monthlyTransactions->filter(
                function ($transaction) use ($month) {
                    return $transaction->transaction_date->format('Y-m') ===
                        $month->format('Y-m');
                }
            );

            $chartData->push([
                'month' => $month->translatedFormat('M Y'),

                'income' => (float) $monthTransactions
                    ->where('type', 'income')
                    ->sum('amount'),

                'expense' => (float) $monthTransactions
                    ->where('type', 'expense')
                    ->sum('amount'),
            ]);
        }

        // NILAI MAKSIMUM UNTUK GRAFIK
        $chartMax = max(
            1,
            (float) $chartData->max('income'),
            (float) $chartData->max('expense')
        );

        // LIMA TRANSAKSI TERBARU
        $latestTransactions = Transaction::query()
            ->where('user_id', $userId)
            ->with([
                'account',
                'destinationAccount',
                'category',
            ])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'accounts',
            'totalBalance',
            'totalAccounts',
            'totalIncome',
            'totalExpense',
            'chartData',
            'chartMax',
            'latestTransactions'
        ));

    })->name('dashboard');


    // MASTER REKENING
    Route::resource('accounts', AccountController::class)
        ->except(['show']);


    // MASTER KATEGORI
    Route::resource('categories', CategoryController::class)
        ->except(['show']);
    
    //BUDGETS
    Route::resource('budgets', BudgetController::class)->except(['show']);


    // TRANSAKSI
    Route::prefix('transactions')->name('transactions.')->group(function () {

        // PEMASUKAN
        Route::get('/income', [TransactionController::class, 'index'])
            ->defaults('type', 'income')
            ->name('income');

        // PENGELUARAN
        Route::get('/expense', [TransactionController::class, 'index'])
            ->defaults('type', 'expense')
            ->name('expense');

        // TRANSFER
        Route::get('/transfer', [TransactionController::class, 'index'])
            ->defaults('type', 'transfer')
            ->name('transfer');

        // CREATE
        Route::get('/{type}/create', [TransactionController::class, 'create'])
            ->name('create');

        // STORE
        Route::post('/{type}', [TransactionController::class, 'store'])
            ->name('store');

        // EDIT
        Route::get('/{type}/{transaction}/edit', [TransactionController::class, 'edit'])
            ->name('edit');

        // UPDATE
        Route::put('/{type}/{transaction}', [TransactionController::class, 'update'])
            ->name('update');

        // DELETE
        Route::delete('/{type}/{transaction}', [TransactionController::class, 'destroy'])
            ->name('destroy');

    });

});

require __DIR__.'/auth.php';
