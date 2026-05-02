<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'limit_amount',
        'month',
        'year',
        'notified_80',
        'notified_100',
    ];

    protected function casts(): array
    {
        return [
            'limit_amount' => 'decimal:2',
            'month' => 'integer',
            'year' => 'integer',
            'notified_80' => 'boolean',
            'notified_100' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function spentAmount(): float
    {
        return (float) $this->category
            ->transactions()
            ->where('user_id', $this->user_id)
            ->where('type', Transaction::TYPE_EXPENSE)
            ->whereYear('transaction_date', $this->year)
            ->whereMonth('transaction_date', $this->month)
            ->sum('amount');
    }

    public function percentSpent(): float
    {
        if ((float) $this->limit_amount <= 0) {
            return 0.0;
        }
        return round(($this->spentAmount() / (float) $this->limit_amount) * 100, 2);
    }
}
