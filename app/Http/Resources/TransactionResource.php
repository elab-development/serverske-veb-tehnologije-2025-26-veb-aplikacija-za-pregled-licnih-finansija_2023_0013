<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'type'             => $this->type,
            'amount'           => (float) $this->amount,
            'transaction_date' => $this->transaction_date?->format('Y-m-d'),
            'note'             => $this->note,
            'category'         => $this->whenLoaded('category', fn () => [
                'id'    => $this->category->id,
                'name'  => $this->category->name,
                'type'  => $this->category->type,
                'color' => $this->category->color,
                'icon'  => $this->category->icon,
            ]),
            'created_at'       => $this->created_at?->toIso8601String(),
        ];
    }
}
