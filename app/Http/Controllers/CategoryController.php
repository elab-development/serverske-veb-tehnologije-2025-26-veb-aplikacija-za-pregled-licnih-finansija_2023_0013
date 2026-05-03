<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $income = $request->user()->categories()->income()->orderBy('name')->get();
        $expense = $request->user()->categories()->expense()->orderBy('name')->get();

        return view('categories.index', compact('income', 'expense'));
    }

    public function create(): View
    {
        return view('categories.create', ['category' => new Category()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:income,expense',
            'color' => 'required|string|size:7',
            'icon' => 'nullable|string|max:50',
        ]);

        $request->user()->categories()->create($data);

        return redirect()->route('categories.index');
    }

    public function edit(Category $category): View
    {
        Gate::authorize('update', $category);

        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        Gate::authorize('update', $category);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:income,expense',
            'color' => 'required|string|size:7',
            'icon' => 'nullable|string|max:50',
        ]);

        $category->update($data);

        return redirect()->route('categories.index');
    }

    public function destroy(Category $category): RedirectResponse
    {
        Gate::authorize('delete', $category);

        $category->delete();

        return redirect()->route('categories.index');
    }
}
