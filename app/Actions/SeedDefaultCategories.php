<?php

namespace App\Actions;

use App\Models\Category;
use App\Models\User;

class SeedDefaultCategories
{
    public const DEFAULTS = [
        ['name' => 'Plata',     'type' => Category::TYPE_INCOME,  'color' => '#16A34A', 'icon' => 'bi-cash-stack'],
        ['name' => 'Honorar',   'type' => Category::TYPE_INCOME,  'color' => '#0EA5E9', 'icon' => 'bi-briefcase'],
        ['name' => 'Hrana',     'type' => Category::TYPE_EXPENSE, 'color' => '#DC2626', 'icon' => 'bi-cart'],
        ['name' => 'Kirija',    'type' => Category::TYPE_EXPENSE, 'color' => '#7C3AED', 'icon' => 'bi-house'],
        ['name' => 'Racuni',    'type' => Category::TYPE_EXPENSE, 'color' => '#F59E0B', 'icon' => 'bi-receipt'],
        ['name' => 'Transport', 'type' => Category::TYPE_EXPENSE, 'color' => '#0891B2', 'icon' => 'bi-car-front'],
        ['name' => 'Zabava',    'type' => Category::TYPE_EXPENSE, 'color' => '#EC4899', 'icon' => 'bi-controller'],
        ['name' => 'Ostalo',    'type' => Category::TYPE_EXPENSE, 'color' => '#64748B', 'icon' => 'bi-three-dots'],
    ];

    public function handle(User $user): void
    {
        foreach (self::DEFAULTS as $category) {
            $user->categories()->create($category);
        }
    }
}
