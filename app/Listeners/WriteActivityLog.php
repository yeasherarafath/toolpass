<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class WriteActivityLog implements ShouldQueue
{
    public function handle($event): void
    {
        if (! method_exists($event, 'activityDescription')) {
            return;
        }

        $subject = method_exists($event, 'activitySubject') ? $event->activitySubject() : null;

        ActivityLog::create([
            'user_id' => $subject?->user_id ?? $subject?->provided_by ?? null,
            'description' => $event->activityDescription(),
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->getKey(),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'metadata' => [],
        ]);
    }
}
