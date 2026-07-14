<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Actions\Review\ModerateReviewAction;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'package'])->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $reviews = $query->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function moderate(Request $request, Review $review)
    {
        $validated = $request->validate([
            'decision' => ['required', 'in:approve,reject'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        app(ModerateReviewAction::class)->handle($review, $validated['decision'], Auth::user(), $validated['reason'] ?? null);

        return back()->with('success', 'Review ' . $validated['decision'] . 'd.');
    }
}
