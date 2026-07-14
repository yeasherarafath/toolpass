<?php

namespace App\Actions\AdminTask;

use App\Models\AdminTask;

class CreateAdminTaskAction
{
    public function handle(array $data): AdminTask
    {
        $type = $data['type'];
        $status = $data['status'] ?? 'open';

        $query = AdminTask::where('type', $type)
            ->whereIn('status', ['open', 'in_progress']);

        if (! empty($data['order_id'])) {
            $query->where('order_id', $data['order_id']);
        }
        if (! empty($data['user_tool_access_id'])) {
            $query->where('user_tool_access_id', $data['user_tool_access_id']);
        }
        if (! empty($data['otp_request_id'])) {
            $query->where('otp_request_id', $data['otp_request_id']);
        }
        if (! empty($data['device_reset_request_id'])) {
            $query->where('device_reset_request_id', $data['device_reset_request_id']);
        }

        $existing = $query->first();
        if ($existing) {
            return $existing;
        }

        return AdminTask::create([
            'user_id' => $data['user_id'] ?? null,
            'order_id' => $data['order_id'] ?? null,
            'user_tool_access_id' => $data['user_tool_access_id'] ?? null,
            'otp_request_id' => $data['otp_request_id'] ?? null,
            'device_reset_request_id' => $data['device_reset_request_id'] ?? null,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'type' => $type,
            'priority' => $data['priority'] ?? 'medium',
            'status' => $status,
            'assigned_to' => $data['assigned_to'] ?? null,
            'due_at' => $data['due_at'] ?? null,
        ]);
    }
}
