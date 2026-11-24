<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
        ]);

        $month = (int) ($validated['month'] ?? now()->month);
        $year = (int) ($validated['year'] ?? now()->year);

        $budgets = Budget::with('category')
            ->where('user_id', Auth::id())
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('category_id')
            ->get();

        foreach ($budgets as $budget) {
            $budget->realization = Transaction::where('user_id', Auth::id())
                ->where('category_id', $budget->category_id)
                ->where('type', 'expense')
                ->whereMonth('transaction_date', $month)
                ->whereYear('transaction_date', $year)
                ->sum('amount');

            $budget->remaining = (float) $budget->amount
                - (float) $budget->realization;

            $budget->percentage = (float) $budget->amount > 0
                ? ((float) $budget->realization / (float) $budget->amount) * 100
                : 0;
        }

        $categories = Category::where('user_id', Auth::id())
            ->where('type', 'expense')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $totalBudget = $budgets->sum(fn ($item) => (float) $item->amount);
        $totalRealization = $budgets->sum(fn ($item) => (float) $item->realization);
        $totalRemaining = $totalBudget - $totalRealization;

        return view('budgets.index', compact(
            'budgets',
            'categories',
            'month',
            'year',
            'totalBudget',
            'totalRealization',
            'totalRemaining'
        ));
    }

    public function create()
    {
        $categories = Category::where('user_id', Auth::id())
            ->where('type', 'expense')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('budgets.form', [
            'budget' => new Budget(),
            'categories' => $categories,
            'month' => (int) request('month', now()->month),
            'year' => (int) request('year', now()->year),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateBudget($request);

        Budget::create([
            ...$data,
            'user_id' => Auth::id(),
        ]);

        return redirect()
            ->route('budgets.index', [
                'month' => $data['month'],
                'year' => $data['year'],
            ])
            ->with('success', 'Anggaran berhasil ditambahkan.');
    }

    public function edit(Budget $budget)
    {
        $budget = $this->findUserBudget($budget);

        $categories = Category::where('user_id', Auth::id())
            ->where('type', 'expense')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('budgets.form', [
            'budget' => $budget,
            'categories' => $categories,
            'month' => $budget->month,
            'year' => $budget->year,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Budget $budget)
    {
        $budget = $this->findUserBudget($budget);
        $data = $this->validateBudget($request, $budget);

        $budget->update($data);

        return redirect()
            ->route('budgets.index', [
                'month' => $data['month'],
                'year' => $data['year'],
            ])
            ->with('success', 'Anggaran berhasil diperbarui.');
    }

    public function destroy(Budget $budget)
    {
        $budget = $this->findUserBudget($budget);

        $month = $budget->month;
        $year = $budget->year;

        $budget->delete();

        return redirect()
            ->route('budgets.index', compact('month', 'year'))
            ->with('success', 'Anggaran berhasil dihapus.');
    }

    private function validateBudget(Request $request, ?Budget $budget = null): array
    {
        return $request->validate([
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(
                    fn ($query) => $query
                        ->where('user_id', Auth::id())
                        ->where('type', 'expense')
                        ->where('is_active', true)
                ),
                Rule::unique('budgets')
                    ->where(fn ($query) => $query
                        ->where('user_id', Auth::id())
                        ->where('month', $request->input('month'))
                        ->where('year', $request->input('year'))
                    )
                    ->ignore($budget?->id),
            ],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:9999999999999999'],
        ], [
            'category_id.required' => 'Kategori pengeluaran wajib dipilih.',
            'category_id.exists' => 'Kategori tidak valid atau tidak aktif.',
            'category_id.unique' => 'Anggaran untuk kategori dan periode tersebut sudah tersedia.',
            'month.required' => 'Bulan wajib dipilih.',
            'year.required' => 'Tahun wajib diisi.',
            'amount.required' => 'Nominal anggaran wajib diisi.',
            'amount.numeric' => 'Nominal anggaran harus berupa angka.',
            'amount.gt' => 'Nominal anggaran harus lebih dari nol.',
        ]);
    }

    private function findUserBudget(Budget $budget): Budget
    {
        return Budget::where('user_id', Auth::id())
            ->findOrFail($budget->id);
    }
}