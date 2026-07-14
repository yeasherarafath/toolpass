<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Actions\Review\CreateReviewAction;
use App\Models\Package;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'package_id' => ['required', 'exists:packages,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:150'],
            'body' => ['nullable', 'string', 'max:2000'],
        ]);

        $package = Package::findOrFail($validated['package_id']);

        try {
            app(CreateReviewAction::class)->handle(Auth::user(), $package, $validated);
        } catch (\Throwable $e) {
            return back()->withErrors(['review' => $e->getMessage()]);
        }

        return back()->with('success', 'Review submitted for moderation.');
    }
}
