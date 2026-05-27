<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BudgetResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BudgetApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $month = (int) $request->query('month', now()->month);
        $year  = (int) $request->query('year', now()->year);

        $budgets = $request->user()->budgets()
            ->with('category')
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        return response()->json([
            'data'    => BudgetResource::collection($budgets),
            'message' => 'OK',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_id'  => ['required', 'integer'],
            'limit_amount' => ['required', 'numeric', 'min:0.01'],
            'month'        => ['required', 'integer', 'min:1', 'max:12'],
            'year'         => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $category = $request->user()->categories()->find($data['category_id']);

        if (! $category) {
            return response()->json(['message' => 'Kategorija nije pronađena.'], 404);
        }

        $budget = $request->user()->budgets()->updateOrCreate(
            [
                'category_id' => $data['category_id'],
                'month'       => $data['month'],
                'year'        => $data['year'],
            ],
            ['limit_amount' => $data['limit_amount']]
        );

        $budget->load('category');

        return response()->json([
            'data'    => new BudgetResource($budget),
            'message' => 'Budžet je sačuvan.',
        ], 201);
    }
}
