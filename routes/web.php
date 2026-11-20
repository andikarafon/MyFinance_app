
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;

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

});

require __DIR__.'/auth.php';
