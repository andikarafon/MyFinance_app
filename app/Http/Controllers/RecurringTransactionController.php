<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\RecurringTransaction;
use App\Models\RecurringTransactionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RecurringTransactionController extends Controller
{
    /**
     * Menampilkan daftar jadwal transaksi berulang.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = RecurringTransaction::query()
            ->where('user_id', $userId)
            ->with(['account', 'category']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('account', function ($accountQuery) use ($search) {
                        $accountQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $recurringTransactions = $query
            ->orderBy('next_run_date')
            ->paginate(15)
            ->withQueryString();

        return view('recurring-transactions.index', compact(
            'recurringTransactions'
        ));
    }
        /**
         * Menampilkan form tambah transaksi berulang.
         */
        public function create()
        {
            $userId = Auth::id();

            // Mengambil rekening aktif milik user.
            $accounts = Account::query()
                ->where('user_id', $userId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            // Mengambil kategori pemasukan dan pengeluaran aktif.
            $categories = Category::query()
                ->where('user_id', $userId)
                ->whereIn('type', ['income', 'expense'])
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            return view('recurring-transactions.create', [
                'accounts' => $accounts,
                'categories' => $categories,
            ]);
        }

    /**
     * Menyimpan jadwal baru.
     */
    public function store(Request $request)
    {
        $validated = $this->validateSchedule($request);

        $nextRunDate = $this->calculateInitialRunDate(
            $validated,
            Carbon::parse($validated['start_date'])
        );

        if (
            $validated['end_date']
            && $nextRunDate->gt(Carbon::parse($validated['end_date'])->startOfDay())
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_date' => 'Tidak ada jadwal transaksi dalam periode yang ditentukan.',
                ]);
        }

        $validated['user_id'] = Auth::id();
        $validated['next_run_date'] = $nextRunDate->toDateString();
        $validated['status'] = 'active';

        RecurringTransaction::create($validated);

        return redirect()
            ->route('recurring-transactions.index')
            ->with('success', 'Jadwal transaksi berulang berhasil ditambahkan.');
    }

    /**
     * Detail jadwal dan histori.
     */
    public function show(RecurringTransaction $recurringTransaction)
    {
        $this->authorizeSchedule($recurringTransaction);

        $recurringTransaction->load([
            'account',
            'category',
        ]);

        $logs = $recurringTransaction->logs()
            ->orderByDesc('scheduled_date')
            ->paginate(15);

        return view('recurring-transactions.show', compact(
            'recurringTransaction',
            'logs'
        ));
    }


        /**
         * Menampilkan form edit transaksi berulang.
         */
        public function edit(RecurringTransaction $recurringTransaction)
        {
            // Pastikan jadwal milik user yang sedang login.
            $this->authorizeSchedule($recurringTransaction);

            // Ambil rekening aktif milik user.
            $accounts = Account::query()
                ->where('user_id', Auth::id())
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            // Ambil kategori pemasukan dan pengeluaran aktif.
            $categories = Category::query()
                ->where('user_id', Auth::id())
                ->whereIn('type', ['income', 'expense'])
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            return view('recurring-transactions.edit', [
                'recurringTransaction' => $recurringTransaction,
                'accounts' => $accounts,
                'categories' => $categories,
            ]);
        }


    /**
     * Memperbarui jadwal.
     *
     * Perubahan hanya berlaku untuk periode berikutnya.
     * Histori dan transaksi yang sudah terbentuk tidak diubah.
     */
    public function update(
        Request $request,
        RecurringTransaction $recurringTransaction
    ) {
        $this->authorizeSchedule($recurringTransaction);

        if ($recurringTransaction->status === 'completed') {
            abort(403, 'Jadwal yang sudah selesai tidak dapat diubah.');
        }

        $validated = $this->validateSchedule($request);

        $nextRunDate = $this->calculateInitialRunDate(
            $validated,
            Carbon::today()
        );

        if (
            $validated['end_date']
            && $nextRunDate->gt(Carbon::parse($validated['end_date'])->startOfDay())
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_date' => 'Tidak ada jadwal transaksi berikutnya dalam periode yang ditentukan.',
                ]);
        }

        $validated['next_run_date'] = $nextRunDate->toDateString();

        $recurringTransaction->update($validated);

        // return redirect()
        //     ->route('recurring-transactions.show', $recurringTransaction)
        //     ->with('success', 'Jadwal transaksi berhasil diperbarui.');

        return redirect()
            ->route('recurring-transactions.index')
            ->with('success', 'Jadwal transaksi berhasil diperbarui.');
    }

    /**
     * Soft Delete jadwal.
     * Histori tetap disimpan.
     * Pending otomatis dibatalkan.
     */
    public function destroy(RecurringTransaction $recurringTransaction)
    {
        $this->authorizeSchedule($recurringTransaction);

        DB::transaction(function () use ($recurringTransaction) {

            $schedule = RecurringTransaction::query()
                ->whereKey($recurringTransaction->id)
                ->lockForUpdate()
                ->firstOrFail();

            $schedule->logs()
                ->where('status', 'pending')
                ->update([
                    'status' => 'cancelled',
                    'updated_at' => now(),
                ]);

            $schedule->delete();
        });

        return redirect()
            ->route('recurring-transactions.index')
            ->with('success', 'Jadwal berhasil dihapus. Riwayat tetap tersimpan.');
    }

    /**
     * Mengaktifkan jadwal.
     */
    public function activate(RecurringTransaction $recurringTransaction)
    {
        $this->authorizeSchedule($recurringTransaction);

        if ($recurringTransaction->status === 'completed') {
            abort(403, 'Jadwal yang sudah selesai tidak dapat diaktifkan.');
        }

        $recurringTransaction->update([
            'status' => 'active',
        ]);

        return back()->with('success', 'Jadwal berhasil diaktifkan.');
    }

    /**
     * Menonaktifkan jadwal.
     * Seluruh Pending otomatis dibatalkan.
     */
    public function deactivate(RecurringTransaction $recurringTransaction)
    {
        $this->authorizeSchedule($recurringTransaction);

        DB::transaction(function () use ($recurringTransaction) {

            $schedule = RecurringTransaction::query()
                ->whereKey($recurringTransaction->id)
                ->lockForUpdate()
                ->firstOrFail();

            $schedule->update([
                'status' => 'inactive',
            ]);

            $schedule->logs()
                ->where('status', 'pending')
                ->update([
                    'status' => 'cancelled',
                    'updated_at' => now(),
                ]);
        });

        return back()->with('success', 'Jadwal berhasil dinonaktifkan.');
    }

    /**
     * Konfirmasi transaksi Pending.
     */
    public function confirm(Request $request, RecurringTransactionLog $log)
    {
        $validated = $request->validate([
            'amount' => [
                'nullable',
                'numeric',
                'gt:0',
                'max:9999999999999999',
            ],
        ], [
            'amount.numeric' => 'Nominal harus berupa angka.',
            'amount.gt' => 'Nominal harus lebih besar dari nol.',
        ]);

        DB::transaction(function () use ($log, $validated) {

            $pendingLog = RecurringTransactionLog::query()
                ->whereKey($log->id)
                ->lockForUpdate()
                ->firstOrFail();

            $schedule = RecurringTransaction::withTrashed()
                ->find($pendingLog->recurring_transaction_id);

            abort_unless(
                $schedule && $schedule->user_id === Auth::id(),
                403,
                'Anda tidak memiliki akses ke transaksi ini.'
            );

            abort_unless(
                $pendingLog->status === 'pending'
                && $schedule->status === 'active'
                && !$schedule->trashed(),
                403,
                'Transaksi ini tidak dapat dikonfirmasi.'
            );

            $account = Account::query()
                ->where('id', $pendingLog->account_id)
                ->where('user_id', $schedule->user_id)
                ->where('is_active', true)
                ->first();

            abort_unless(
                $account,
                422,
                'Rekening tidak tersedia atau tidak aktif.'
            );

            if ($pendingLog->category_id !== null) {
                $category = Category::query()
                    ->where('id', $pendingLog->category_id)
                    ->where('user_id', $schedule->user_id)
                    ->where('type', $pendingLog->type)
                    ->where('is_active', true)
                    ->first();

                abort_unless(
                    $category,
                    422,
                    'Kategori tidak tersedia atau tidak aktif.'
                );
            }

            $amount = $validated['amount']
                ?? $pendingLog->amount;

            $transaction = Transaction::create([
                'user_id' => $schedule->user_id,
                'account_id' => $pendingLog->account_id,
                'destination_account_id' => null,
                'category_id' => $pendingLog->category_id,
                'type' => $pendingLog->type,
                'amount' => $amount,
                'transaction_date' => $pendingLog->scheduled_date,
                'description' => $pendingLog->name,
                'notes' => $pendingLog->notes,
            ]);

            $pendingLog->update([
                'transaction_id' => $transaction->id,
                'amount' => $amount,
                'transaction_date' => $pendingLog->scheduled_date,
                'status' => 'generated',
                'confirmed_at' => now(),
                'generated_at' => now(),
            ]);
        });

        return back()->with(
            'success',
            'Transaksi berulang berhasil dikonfirmasi dan dibuat.'
        );
    }

    /**
     * Membatalkan transaksi Pending.
     */
    public function cancel(RecurringTransactionLog $log)
    {
        $pendingLog = RecurringTransactionLog::with(
            'recurringTransaction'
        )->findOrFail($log->id);

        $schedule = $pendingLog->recurringTransaction;

        abort_unless(
            $schedule && $schedule->user_id === Auth::id(),
            403,
            'Anda tidak memiliki akses ke transaksi ini.'
        );

        abort_unless(
            $pendingLog->status === 'pending',
            403,
            'Hanya transaksi Pending yang dapat dibatalkan.'
        );

        $pendingLog->update([
            'status' => 'cancelled',
        ]);

        return back()->with(
            'success',
            'Transaksi Pending berhasil dibatalkan.'
        );
    }

    /**
     * Validasi data jadwal.
     */
    private function validateSchedule(Request $request): array
    {
        $userId = Auth::id();

        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'type' => [
                'required',
                Rule::in(['income', 'expense']),
            ],

            'account_id' => [
                'required',
                Rule::exists('accounts', 'id')->where(
                    fn ($query) => $query
                        ->where('user_id', $userId)
                        ->where('is_active', true)
                ),
            ],

            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(
                    fn ($query) => $query
                        ->where('user_id', $userId)
                        ->where('type', $request->input('type'))
                        ->where('is_active', true)
                ),
            ],

            'amount' => [
                'required',
                'numeric',
                'gt:0',
                'max:9999999999999999',
            ],

            'frequency' => [
                'required',
                Rule::in(['daily', 'weekly', 'monthly', 'yearly']),
            ],

            'interval' => [
                'required',
                'integer',
                'min:1',
            ],

            'day_of_week' => [
                'nullable',
                'required_if:frequency,weekly',
                'integer',
                'between:1,7',
            ],

            'day_of_month' => [
                'nullable',
                'required_if:frequency,monthly,yearly',
                'integer',
                'between:1,31',
            ],

            'month_of_year' => [
                'nullable',
                'required_if:frequency,yearly',
                'integer',
                'between:1,12',
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

            'confirmation_required' => [
                'required',
                'boolean',
            ],

            'reminder_days' => [
                'required',
                'integer',
                'between:0,365',
            ],
        ], [
            'name.required' => 'Nama jadwal wajib diisi.',
            'type.required' => 'Jenis transaksi wajib dipilih.',
            'account_id.required' => 'Rekening wajib dipilih.',
            'account_id.exists' => 'Rekening tidak valid atau tidak aktif.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori tidak valid atau tidak aktif.',
            'amount.required' => 'Nominal wajib diisi.',
            'amount.numeric' => 'Nominal harus berupa angka.',
            'amount.gt' => 'Nominal harus lebih besar dari nol.',
            'frequency.required' => 'Frekuensi wajib dipilih.',
            'interval.min' => 'Interval minimal 1.',
            'day_of_week.required_if' => 'Hari transaksi wajib dipilih.',
            'day_of_month.required_if' => 'Tanggal transaksi wajib diisi.',
            'month_of_year.required_if' => 'Bulan transaksi wajib dipilih.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal berakhir tidak boleh sebelum tanggal mulai.',
            'reminder_days.between' => 'Pengingat harus antara 0 sampai 365 hari.',
        ]);
    }

    /**
     * Menghitung tanggal transaksi pertama atau berikutnya
     * berdasarkan konfigurasi jadwal.
     */
    private function calculateInitialRunDate(
        array $data,
        Carbon $baseDate
    ): Carbon {
        $date = $baseDate->copy()->startOfDay();

        switch ($data['frequency']) {
            case 'daily':
                return $date;

            case 'weekly':
                $targetDay = (int) $data['day_of_week'];

                $difference = (
                    $targetDay - $date->dayOfWeekIso + 7
                ) % 7;

                return $date->addDays($difference);

            case 'monthly':
                $day = (int) $data['day_of_month'];

                $candidate = $date->copy()
                    ->startOfMonth()
                    ->day(min($day, $date->daysInMonth));

                if ($candidate->lt($date)) {
                    $candidate = $date->copy()
                        ->addMonthsNoOverflow((int) $data['interval'])
                        ->startOfMonth();

                    $candidate->day = min(
                        $day,
                        $candidate->daysInMonth
                    );
                }

                return $candidate;

            case 'yearly':
                $day = (int) $data['day_of_month'];
                $month = (int) $data['month_of_year'];

                $candidate = Carbon::create(
                    $date->year,
                    $month,
                    1
                );

                $candidate->day = min(
                    $day,
                    $candidate->daysInMonth
                );

                if ($candidate->lt($date)) {
                    $candidate = Carbon::create(
                        $date->year + (int) $data['interval'],
                        $month,
                        1
                    );

                    $candidate->day = min(
                        $day,
                        $candidate->daysInMonth
                    );
                }

                return $candidate;

            default:
                abort(422, 'Frekuensi transaksi tidak valid.');
        }
    }

    /**
     * Mengambil rekening dan kategori aktif milik user.
     */
    private function getPageData(int $userId): array
    {
        $accounts = Account::query()
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $incomeCategories = Category::query()
            ->where('user_id', $userId)
            ->where('type', 'income')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $expenseCategories = Category::query()
            ->where('user_id', $userId)
            ->where('type', 'expense')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return compact(
            'accounts',
            'incomeCategories',
            'expenseCategories'
        );
    }

    /**
     * Memastikan jadwal milik user yang sedang login.
     */
    private function authorizeSchedule(
        RecurringTransaction $recurringTransaction
    ): void {
        abort_unless(
            $recurringTransaction->user_id === Auth::id(),
            403,
            'Anda tidak memiliki akses ke jadwal ini.'
        );
    }
}
