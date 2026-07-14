<?php

namespace App\Actions\Review;

use App\Models\Order;
use App\Models\Package;
use App\Models\Review;
use App\Models\User;
use App\Models\UserToolAccess;

class CreateReviewAction
{
    public function handle(User $user, Package $package, array $data): Review
    {
        $hasAccess = UserToolAccess::where('user_id', $user->id)
            ->where('delivery_status', 'delivered')
            ->whereHas('order', fn ($q) => $q->where('package_id', $package->id))
            ->exists();

        if (! $hasAccess) {
            throw new \RuntimeException('You can only review packages you have active access to.');
        }

        return Review::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'body' => $data['body'] ?? null,
            'status' => 'pending',
        ]);
    }
}
