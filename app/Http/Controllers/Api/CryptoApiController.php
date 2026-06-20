<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CryptoApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $data = Cache::remember('crypto_prices', 300, function () {
                $response = Http::get('https://api.coingecko.com/api/v3/simple/price', [
                    'ids'           => 'bitcoin,ethereum',
                    'vs_currencies' => 'eur,usd',
                ]);

                if ($response->failed()) {
                    return null;
                }

                return $response->json();
            });

            if (! $data) {
                return response()->json(
                    ['message' => 'Servis za kripto kurseve trenutno nije dostupan.'],
                    503
                );
            }

            return response()->json([
                'data' => [
                    'bitcoin'  => $data['bitcoin'] ?? null,
                    'ethereum' => $data['ethereum'] ?? null,
                ],
                'message' => 'OK',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                ['message' => 'Servis za kripto kurseve trenutno nije dostupan.'],
                503
            );
        }
    }
}
