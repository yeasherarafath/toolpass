<?php

namespace App\Actions\Announcement;

use App\Models\Announcement;
use App\Models\User;

class CreateAnnouncementAction
{
    public function handle(User $admin, array $data): Announcement
    {
        return Announcement::create([
            'title' => $data['title'],
            'message' => $data['message'],
            'type' => $data['type'] ?? 'info',
            'status' => $data['status'] ?? 'active',
            'visible_to' => $data['visible_to'] ?? 'all',
            'starts_at' => $data['starts_at'] ?? now(),
            'ends_at' => $data['ends_at'] ?? null,
            'created_by' => $admin->id,
        ]);
    }
}
