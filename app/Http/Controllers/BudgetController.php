<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function index(Request $request): View
    {
        $month = (int) ($request->query('month') ?: now()->month);
        $year = (int) ($request->query('year') ?: now()->year);

        $budgets = $request->user()->budgets()
            ->with('category')
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        $expenseCategories = $request->user()->categories()
            ->where('type', Category::TYPE_EXPENSE)
            ->orderBy('name')
            ->get();

        return view('budgets.index', compact('budgets', 'expenseCategories', 'month', 'year'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => 'required|integer',
            'limit_amount' => 'required|numeric|min:0.01',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020|max:2100',
        ]);

        $request->user()->budgets()->create($data);

        return redirect()->route('budgets.index', ['month' => $data['month'], 'year' => $data['year']]);
    }

    public function update(Request $request, Budget $budget): RedirectResponse
    {
        Gate::authorize('update', $budget);

        $data = $request->validate([
            'limit_amount' => 'required|numeric|min:0.01',
        ]);

        $budget->update($data);

        return redirect()->route('budgets.index', ['month' => $budget->month, 'year' => $budget->year]);
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        Gate::authorize('delete', $budget);

        [$month, $year] = [$budget->month, $budget->year];
        $budget->delete();

        return redirect()->route('budgets.index', ['month' => $month, 'year' => $year]);
    }
}
