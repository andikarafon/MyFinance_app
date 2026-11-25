<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


/*
|--------------------------------------------------------------------------
| Recurring Transactions Scheduler
|--------------------------------------------------------------------------
|
| Menjalankan pemrosesan transaksi berulang setiap hari
| pukul 00:05 WIB.
|
*/

Schedule::command('recurring:process')
    ->dailyAt('00:05')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping(10);
