<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'month'         => $this->month,
            'year'          => $this->year,
            'limit_amount'  => (float) $this->limit_amount,
            'spent_amount'  => $this->spentAmount(),
            'percent_spent' => $this->percentSpent(),
            'category'      => $this->whenLoaded('category', fn () => [
                'id'    => $this->category->id,
                'name'  => $this->category->name,
                'color' => $this->category->color,
                'icon'  => $this->category->icon,
            ]),
        ];
    }
}
