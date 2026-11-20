
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;

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

});

require __DIR__.'/auth.php';
