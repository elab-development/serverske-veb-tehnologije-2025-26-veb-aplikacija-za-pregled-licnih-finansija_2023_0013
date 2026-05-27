<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
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
}
