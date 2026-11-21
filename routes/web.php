
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    // MASTER REKENING
    Route::resource('accounts', AccountController::class)
        ->except(['show']);

    // Master Category
    Route::resource('categories', CategoryController::class)
        ->except(['show']);


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
