<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Daftar kategori.
     */
    
        public function index()
        {
            $categories = Category::where('user_id', Auth::id())
                ->orderBy('type')
                ->orderBy('name')
                ->get();

            $totalIncome = $categories
                ->where('type', 'income')
                ->count();

            $totalExpense = $categories
                ->where('type', 'expense')
                ->count();

            return view('categories.index', compact(
                'categories',
                'totalIncome',
                'totalExpense'
            ));
        }


    /**
     * Form tambah kategori.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Simpan kategori.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::in([
                'income',
                'expense',
            ])],
            'icon' => ['nullable', 'string', 'max:50'],
            'color' => [
                'required',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_active'] = true;

        Category::create($validated);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Form edit kategori.
     */
    public function edit(Category $category)
    {
        $this->authorizeCategory($category);

        return view('categories.edit', compact('category'));
    }

    /**
     * Update kategori.
     */
    public function update(Request $request, Category $category)
    {
        $this->authorizeCategory($category);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::in([
                'income',
                'expense',
            ])],
            'icon' => ['nullable', 'string', 'max:50'],
            'color' => [
                'required',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['required', 'boolean'],
        ]);

        $category->update($validated);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Hapus kategori.
     */
    public function destroy(Category $category)
    {
        $this->authorizeCategory($category);

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }

    /**
     * Validasi kepemilikan kategori.
     */
    private function authorizeCategory(Category $category): void
    {
        abort_unless(
            $category->user_id === Auth::id(),
            403,
            'Anda tidak memiliki akses ke kategori ini.'
        );
    }
}
