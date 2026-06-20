<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ExchangeRateApiController extends Controller
{
    private const CURRENCIES = ['EUR', 'USD', 'GBP', 'CHF'];

    public function index(Request $request): JsonResponse
    {
        try {
            $data = Cache::remember('exchange_rates_rsd', 3600, function () {
                $response = Http::get('https://open.er-api.com/v6/latest/RSD');

                if ($response->failed()) {
                    return null;
                }

                return $response->json();
            });

            if (! $data) {
                return response()->json(
                    ['message' => 'Servis za kurseve trenutno nije dostupan.'],
                    503
                );
            }

            $rates = array_intersect_key($data['rates'] ?? [], array_flip(self::CURRENCIES));

            return response()->json([
                'data' => [
                    'base'  => 'RSD',
                    'date'  => $data['time_last_update_utc'] ?? null,
                    'rates' => $rates,
                ],
                'message' => 'OK',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                ['message' => 'Servis za kurseve trenutno nije dostupan.'],
                503
            );
        }
    }

    public function convertBalance(Request $request): JsonResponse
    {
        $currency = strtoupper($request->query('currency', 'EUR'));

        $user = $request->user();
        $transactions = $user->transactions()->get();

        $balanceRsd = $transactions->sum(function ($t) {
            return $t->type === 'income' ? $t->amount : -$t->amount;
        });

        try {
            $data = Cache::remember('exchange_rates_rsd', 3600, function () {
                $response = Http::get('https://open.er-api.com/v6/latest/RSD');

                if ($response->failed()) {
                    return null;
                }

                return $response->json();
            });

            if (! $data) {
                return response()->json(
                    ['message' => 'Servis za kurseve trenutno nije dostupan.'],
                    503
                );
            }

            $rates = $data['rates'] ?? [];

            if (! array_key_exists($currency, $rates)) {
                return response()->json(
                    ['message' => "Valuta '{$currency}' nije pronađena u kursnoj listi."],
                    404
                );
            }

            $rate = $rates[$currency];
            $converted = round($balanceRsd * $rate, 2);

            return response()->json([
                'data' => [
                    'balance_rsd' => round($balanceRsd, 2),
                    'currency'    => $currency,
                    'rate'        => $rate,
                    'converted'   => $converted,
                ],
                'message' => 'OK',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                ['message' => 'Servis za kurseve trenutno nije dostupan.'],
                503
            );
        }
    }
}
