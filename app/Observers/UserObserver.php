<?php

namespace App\Observers;

use App\Actions\SeedDefaultCategories;
use App\Models\User;

class UserObserver
{
    public function __construct(private readonly SeedDefaultCategories $seedDefaults)
    {
    }

    public function created(User $user): void
    {
        $this->seedDefaults->handle($user);
    }
}
