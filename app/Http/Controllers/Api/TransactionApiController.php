<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TransactionApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min((int) $request->query('per_page', 15), 100);
        $perPage = max($perPage, 1);

        $transactions = $request->user()->transactions()
            ->with('category')
            ->ofType($request->query('type'))
            ->ofCategory($request->query('category_id') ? (int) $request->query('category_id') : null)
            ->betweenDates($request->query('from'), $request->query('to'))
            ->searchNote($request->query('q'))
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate($perPage);

        return TransactionResource::collection($transactions);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_id'      => ['required', 'integer'],
            'type'             => ['required', 'in:income,expense'],
            'amount'           => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
            'note'             => ['nullable', 'string', 'max:1000'],
        ]);

        $category = $request->user()->categories()->find($data['category_id']);

        if (! $category) {
            return response()->json(['message' => 'Kategorija nije pronađena.'], 404);
        }

        if ($category->type !== $data['type']) {
            return response()->json([
                'message' => 'Validacijska greška.',
                'errors'  => ['category_id' => ['Kategorija ne odgovara tipu transakcije.']],
            ], 422);
        }

        $transaction = $request->user()->transactions()->create($data);
        $transaction->load('category');

        return response()->json([
            'data'    => new TransactionResource($transaction),
            'message' => 'Transakcija je dodata.',
        ], 201);
    }

    public function show(Request $request, Transaction $transaction): JsonResponse
    {
        if ($transaction->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Zabranjen pristup.'], 403);
        }

        $transaction->load('category');

        return response()->json([
            'data'    => new TransactionResource($transaction),
            'message' => 'OK',
        ]);
    }

    public function update(Request $request, Transaction $transaction): JsonResponse
    {
        if ($transaction->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Zabranjen pristup.'], 403);
        }

        $data = $request->validate([
            'category_id'      => ['required', 'integer'],
            'type'             => ['required', 'in:income,expense'],
            'amount'           => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
            'note'             => ['nullable', 'string', 'max:1000'],
        ]);

        $category = $request->user()->categories()->find($data['category_id']);

        if (! $category) {
            return response()->json(['message' => 'Kategorija nije pronađena.'], 404);
        }

        if ($category->type !== $data['type']) {
            return response()->json([
                'message' => 'Validacijska greška.',
                'errors'  => ['category_id' => ['Kategorija ne odgovara tipu transakcije.']],
            ], 422);
        }

        $transaction->update($data);
        $transaction->load('category');

        return response()->json([
            'data'    => new TransactionResource($transaction),
            'message' => 'Transakcija je izmijenjena.',
        ]);
    }

    public function destroy(Request $request, Transaction $transaction): JsonResponse
    {
        if ($transaction->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Zabranjen pristup.'], 403);
        }

        $transaction->delete();

        return response()->json(['message' => 'Transakcija je obrisana.']);
    }
}
