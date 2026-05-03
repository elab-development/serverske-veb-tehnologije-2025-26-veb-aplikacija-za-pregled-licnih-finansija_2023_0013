<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
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

    public function store(CategoryRequest $request): RedirectResponse
    {
        $request->user()->categories()->create($request->validated());

        return redirect()->route('categories.index');
    }

    public function edit(Category $category): View
    {
        Gate::authorize('update', $category);

        return view('categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        Gate::authorize('update', $category);

        $category->update($request->validated());

        return redirect()->route('categories.index');
    }

    public function destroy(Category $category): RedirectResponse
    {
        Gate::authorize('delete', $category);

        $count = $category->transactions()->count();
        if ($count > 0) {
            return redirect()->route('categories.index')
                ->with('error', "Ova kategorija ima {$count} transakcija, prvo ih premestite ili obrisite.");
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Kategorija je obrisana.');
    }
}
