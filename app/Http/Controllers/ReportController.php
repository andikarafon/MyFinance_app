<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Exports\FinancialReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * HALAMAN LAPORAN UTAMA
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
            'account_id' => [
                'nullable',
                'integer',
                Rule::exists('accounts', 'id')->where(
                    fn ($query) => $query->where('user_id', $userId)
                ),
            ],
        ]);

        $month = (int) ($validated['month'] ?? now()->month);
        $year = (int) ($validated['year'] ?? now()->year);
        $selectedAccountId = $validated['account_id'] ?? null;

        $startDate = Carbon::createFromDate($year, $month, 1)
            ->startOfMonth();

        $endDate = $startDate->copy()->endOfMonth();

        $accounts = Account::query()
            ->where('user_id', $userId)
            ->orderBy('name')
            ->get();

        $baseQuery = Transaction::query()
            ->where('user_id', $userId)
            ->whereIn('type', ['income', 'expense'])
            ->whereBetween('transaction_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ]);

        if ($selectedAccountId) {
            $baseQuery->where('account_id', $selectedAccountId);
        }

        $totalIncome = (clone $baseQuery)
            ->where('type', 'income')
            ->sum('amount');

        $totalExpense = (clone $baseQuery)
            ->where('type', 'expense')
            ->sum('amount');

        $netTotal = (float) $totalIncome - (float) $totalExpense;

        $totalTransactions = (clone $baseQuery)->count();

        $incomeByCategory = (clone $baseQuery)
            ->with('category')
            ->where('type', 'income')
            ->select('category_id')
            ->selectRaw('SUM(amount) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get();

        $expenseByCategory = (clone $baseQuery)
            ->with('category')
            ->where('type', 'expense')
            ->select('category_id')
            ->selectRaw('SUM(amount) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get();

        $transactions = (clone $baseQuery)
            ->with(['account', 'category'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $reportPeriod = $startDate->translatedFormat('F Y');

        return view('reports.index', compact(
            'month',
            'year',
            'selectedAccountId',
            'accounts',
            'startDate',
            'endDate',
            'reportPeriod',
            'totalIncome',
            'totalExpense',
            'netTotal',
            'totalTransactions',
            'incomeByCategory',
            'expenseByCategory',
            'transactions',
        ));
    }


    /**
     * EXPORT LAPORAN KE EXCEL
     */
    public function exportExcel(Request $request)
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
            'account_id' => [
                'nullable',
                'integer',
                Rule::exists('accounts', 'id')->where(
                    fn ($query) => $query->where('user_id', $userId)
                ),
            ],
        ]);

        $month = (int) ($validated['month'] ?? now()->month);
        $year = (int) ($validated['year'] ?? now()->year);
        $selectedAccountId = $validated['account_id'] ?? null;

        $startDate = Carbon::createFromDate($year, $month, 1)
            ->startOfMonth();

        $endDate = $startDate->copy()->endOfMonth();

        $accounts = Account::query()
            ->where('user_id', $userId)
            ->orderBy('name')
            ->get();

        $baseQuery = Transaction::query()
            ->where('user_id', $userId)
            ->whereIn('type', ['income', 'expense'])
            ->whereBetween('transaction_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ]);

        if ($selectedAccountId) {
            $baseQuery->where('account_id', $selectedAccountId);
        }

        $totalIncome = (float) (clone $baseQuery)
            ->where('type', 'income')
            ->sum('amount');

        $totalExpense = (float) (clone $baseQuery)
            ->where('type', 'expense')
            ->sum('amount');

        $transactions = (clone $baseQuery)
            ->with(['account', 'category'])
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        // HITUNG SALDO AWAL
        if ($selectedAccountId) {
            $selectedAccount = $accounts->firstWhere(
                'id',
                $selectedAccountId
            );

            $openingBalance = (float) (
                $selectedAccount->opening_balance ?? 0
            );

            

            $historicalIncome = Transaction::query()
                ->where('user_id', $userId)
                ->where('account_id', $selectedAccountId)
                ->where('type', 'income')
                ->where('transaction_date', '<', $startDate->toDateString())
                ->sum('amount');

            $historicalExpense = Transaction::query()
                ->where('user_id', $userId)
                ->where('account_id', $selectedAccountId)
                ->where('type', 'expense')
                ->where('transaction_date', '<', $startDate->toDateString())
                ->sum('amount');

            $historicalTransferOut = Transaction::query()
                ->where('user_id', $userId)
                ->where('account_id', $selectedAccountId)
                ->where('type', 'transfer')
                ->where('transaction_date', '<', $startDate->toDateString())
                ->sum('amount');

            $historicalTransferIn = Transaction::query()
                ->where('user_id', $userId)
                ->where('destination_account_id', $selectedAccountId)
                ->where('type', 'transfer')
                ->where('transaction_date', '<', $startDate->toDateString())
                ->sum('amount');

            $openingBalance +=
                (float) $historicalIncome
                - (float) $historicalExpense
                - (float) $historicalTransferOut
                + (float) $historicalTransferIn;

            $accountName = $selectedAccount->name ?? 'Rekening';

           

        } else {
            $openingBalance = (float) $accounts->sum('opening_balance');

            $historicalIncome = Transaction::query()
                ->where('user_id', $userId)
                ->where('type', 'income')
                ->where('transaction_date', '<', $startDate->toDateString())
                ->sum('amount');

            $historicalExpense = Transaction::query()
                ->where('user_id', $userId)
                ->where('type', 'expense')
                ->where('transaction_date', '<', $startDate->toDateString())
                ->sum('amount');

            $openingBalance +=
                (float) $historicalIncome
                - (float) $historicalExpense;

            $accountName = 'Semua Rekening';
        }

        $reportPeriod = $startDate->translatedFormat('F Y');


        $export = new FinancialReportExport(
            $transactions,
            (float) $openingBalance,
            $totalIncome,
            $totalExpense,
            $reportPeriod,
            $accountName
        );

        $fileName = 'Laporan-Keuangan-'
            . $year . '-'
            . str_pad((string) $month, 2, '0', STR_PAD_LEFT)
            . '.xlsx';

        return Excel::download($export, $fileName);
    }


    /**
     * PREVIEW CETAK LAPORAN
     */
    public function print(Request $request)
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
            'account_id' => [
                'nullable',
                'integer',
                Rule::exists('accounts', 'id')->where(
                    fn ($query) => $query->where('user_id', $userId)
                ),
            ],
        ]);

        $month = (int) ($validated['month'] ?? now()->month);
        $year = (int) ($validated['year'] ?? now()->year);
        $selectedAccountId = $validated['account_id'] ?? null;

        $startDate = Carbon::createFromDate($year, $month, 1)
            ->startOfMonth();

        $endDate = $startDate->copy()->endOfMonth();

        // DATA REKENING MILIK USER
        $accounts = Account::query()
            ->where('user_id', $userId)
            ->orderBy('name')
            ->get();

        // QUERY DASAR TRANSAKSI PERIODE LAPORAN
        $baseQuery = Transaction::query()
            ->where('user_id', $userId)
            ->whereIn('type', ['income', 'expense'])
            ->whereBetween('transaction_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ]);

        if ($selectedAccountId) {
            $baseQuery->where('account_id', $selectedAccountId);
        }

        // TOTAL PEMASUKAN
        $totalIncome = (clone $baseQuery)
            ->where('type', 'income')
            ->sum('amount');

        // TOTAL PENGELUARAN
        $totalExpense = (clone $baseQuery)
            ->where('type', 'expense')
            ->sum('amount');

        // AMBIL SELURUH TRANSAKSI UNTUK CETAK
        $transactions = (clone $baseQuery)
            ->with(['account', 'category'])
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        // =========================================
        // HITUNG SALDO AWAL PERIODE
        // =========================================

        if ($selectedAccountId) {

            // SALDO AWAL REKENING TERPILIH
            $selectedAccount = $accounts->firstWhere(
                'id',
                $selectedAccountId
            );

            $openingBalance = (float) (
                $selectedAccount->opening_balance ?? 0
            );

            // PEMASUKAN SEBELUM PERIODE
            $historicalIncome = Transaction::query()
                ->where('user_id', $userId)
                ->where('account_id', $selectedAccountId)
                ->where('type', 'income')
                ->where('transaction_date', '<', $startDate->toDateString())
                ->sum('amount');

            // PENGELUARAN SEBELUM PERIODE
            $historicalExpense = Transaction::query()
                ->where('user_id', $userId)
                ->where('account_id', $selectedAccountId)
                ->where('type', 'expense')
                ->where('transaction_date', '<', $startDate->toDateString())
                ->sum('amount');

            // TRANSFER KELUAR SEBELUM PERIODE
            $historicalTransferOut = Transaction::query()
                ->where('user_id', $userId)
                ->where('account_id', $selectedAccountId)
                ->where('type', 'transfer')
                ->where('transaction_date', '<', $startDate->toDateString())
                ->sum('amount');

            // TRANSFER MASUK SEBELUM PERIODE
            $historicalTransferIn = Transaction::query()
                ->where('user_id', $userId)
                ->where('destination_account_id', $selectedAccountId)
                ->where('type', 'transfer')
                ->where('transaction_date', '<', $startDate->toDateString())
                ->sum('amount');

            $openingBalance +=
                (float) $historicalIncome
                - (float) $historicalExpense
                - (float) $historicalTransferOut
                + (float) $historicalTransferIn;
            
            
        } else {

            // SALDO AWAL SELURUH REKENING
            $openingBalance = (float) $accounts->sum(
                'opening_balance'
            );

            // TOTAL PEMASUKAN SEBELUM PERIODE
            $historicalIncome = Transaction::query()
                ->where('user_id', $userId)
                ->where('type', 'income')
                ->where('transaction_date', '<', $startDate->toDateString())
                ->sum('amount');

            // TOTAL PENGELUARAN SEBELUM PERIODE
            $historicalExpense = Transaction::query()
                ->where('user_id', $userId)
                ->where('type', 'expense')
                ->where('transaction_date', '<', $startDate->toDateString())
                ->sum('amount');

            // TRANSFER ANTAR REKENING TIDAK MENGUBAH
            // TOTAL SALDO SELURUH REKENING.

            $openingBalance +=
                (float) $historicalIncome
                - (float) $historicalExpense;
        }

        // SALDO AKHIR PERIODE
        $closingBalance = $openingBalance
            + (float) $totalIncome
            - (float) $totalExpense;

        

        $reportPeriod = $startDate->translatedFormat('F Y');

           


        // KIRIM DATA KE HALAMAN PREVIEW
        return view('reports.print', compact(
            'month',
            'year',
            'selectedAccountId',
            'accounts',
            'startDate',
            'endDate',
            'reportPeriod',
            'totalIncome',
            'totalExpense',
            'openingBalance',
            'closingBalance',
            'transactions',
        ));
    }
}
