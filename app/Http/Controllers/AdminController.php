<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::withCount('transactions')->orderBy('name')->get();
        $totalUsers = User::count();
        $totalTransactions = Transaction::count();
        $totalIncome = (float) Transaction::where('type', 'income')->sum('amount');
        $totalExpense = (float) Transaction::where('type', 'expense')->sum('amount');

        return view('admin.index', compact('users', 'totalUsers', 'totalTransactions', 'totalIncome', 'totalExpense'));
    }

    public function toggleActive(User $user): RedirectResponse
    {
        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', $user->is_active ? 'Korisnik je reaktiviran.' : 'Korisnik je deaktiviran.');
    }
}
