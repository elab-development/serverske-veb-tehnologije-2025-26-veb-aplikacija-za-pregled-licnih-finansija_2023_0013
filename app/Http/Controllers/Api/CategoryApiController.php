<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BudgetResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $categories = $request->user()->categories()->orderBy('name')->get();

        return response()->json([
            'data'    => CategoryResource::collection($categories),
            'message' => 'OK',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'type'  => ['required', 'in:income,expense'],
            'color' => ['nullable', 'string', 'max:20'],
            'icon'  => ['nullable', 'string', 'max:50'],
        ]);

        $category = $request->user()->categories()->create($data);

        return response()->json([
            'data'    => new CategoryResource($category),
            'message' => 'Kategorija je kreirana.',
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $category = $request->user()->categories()->find($id);

        if (! $category) {
            return response()->json(['message' => 'Kategorija nije pronađena.'], 404);
        }

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'type'  => ['required', 'in:income,expense'],
            'color' => ['nullable', 'string', 'max:20'],
            'icon'  => ['nullable', 'string', 'max:50'],
        ]);

        $category->update($data);

        return response()->json([
            'data'    => new CategoryResource($category),
            'message' => 'Kategorija je izmijenjena.',
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $category = $request->user()->categories()->find($id);

        if (! $category) {
            return response()->json(['message' => 'Kategorija nije pronađena.'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Kategorija je obrisana.']);
    }

    public function transactions(Request $request, int $id): JsonResponse
    {
        $category = $request->user()->categories()->find($id);

        if (! $category) {
            return response()->json(['message' => 'Kategorija nije pronađena.'], 404);
        }

        $transactions = $category->transactions()->orderBy('transaction_date', 'desc')->paginate(15);

        return response()->json([
            'data'    => TransactionResource::collection($transactions),
            'message' => 'OK',
        ]);
    }

    public function budgets(Request $request, int $id): JsonResponse
    {
        $category = $request->user()->categories()->find($id);

        if (! $category) {
            return response()->json(['message' => 'Kategorija nije pronađena.'], 404);
        }

        $budgets = $category->budgets()->get();

        return response()->json([
            'data'    => BudgetResource::collection($budgets),
            'message' => 'OK',
        ]);
    }
}
