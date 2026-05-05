<?php

namespace App\Http\Controllers;

use App\Http\Requests\BudgetRequest;
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

    public function store(BudgetRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $exists = $request->user()->budgets()
            ->where('category_id', $data['category_id'])
            ->where('month', $data['month'])
            ->where('year', $data['year'])
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['category_id' => 'Vec postoji budzet za ovu kategoriju u izabranom periodu.'])
                ->withInput();
        }

        $request->user()->budgets()->create($data);

        return redirect()->route('budgets.index', ['month' => $data['month'], 'year' => $data['year']])
            ->with('success', 'Budzet je dodat.');
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

        return redirect()->route('budgets.index', ['month' => $month, 'year' => $year])
            ->with('success', 'Budzet je obrisan.');
    }

    public function show(Budget $budget)
    {
        Gate::authorize('view', $budget);

        return response()->json([
            'id' => $budget->id,
            'category_id' => $budget->category_id,
            'limit_amount' => $budget->limit_amount,
            'month' => $budget->month,
            'year' => $budget->year,
        ]);
    }

    public function copyPrevious(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020|max:2100',
        ]);

        $month = (int) $data['month'];
        $year = (int) $data['year'];

        $prevMonth = $month === 1 ? 12 : $month - 1;
        $prevYear = $month === 1 ? $year - 1 : $year;

        $previous = $request->user()->budgets()
            ->where('month', $prevMonth)
            ->where('year', $prevYear)
            ->get();

        if ($previous->isEmpty()) {
            return redirect()->route('budgets.index', ['month' => $month, 'year' => $year])
                ->with('error', 'Prethodni mesec nema budzete za kopiranje.');
        }

        $copied = 0;
        foreach ($previous as $prev) {
            $exists = $request->user()->budgets()
                ->where('category_id', $prev->category_id)
                ->where('month', $month)
                ->where('year', $year)
                ->exists();

            if (! $exists) {
                $request->user()->budgets()->create([
                    'category_id' => $prev->category_id,
                    'limit_amount' => $prev->limit_amount,
                    'month' => $month,
                    'year' => $year,
                ]);
                $copied++;
            }
        }

        return redirect()->route('budgets.index', ['month' => $month, 'year' => $year])
            ->with('success', "Kopirano je {$copied} budzeta iz prethodnog meseca.");
    }
}
