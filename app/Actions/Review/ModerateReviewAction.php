<?php

namespace App\Actions\Review;

use App\Models\Review;
use App\Models\User;

class ModerateReviewAction
{
    public function handle(Review $review, string $decision, User $admin, ?string $reason = null): Review
    {
        $review->status = $decision === 'approve' ? 'approved' : 'rejected';
        $review->save();

        return $review;
    }
}
