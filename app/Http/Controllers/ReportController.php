<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->query('from', now()->startOfMonth()->subMonth()->format('Y-m-d'));
        $to = $request->query('to', now()->format('Y-m-d'));

        return view('reports.index', compact('from', 'to'));
    }
}
