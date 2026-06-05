<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Buat notifikasi baru
     */
    public function create(int $userId, string $title, string $message, string $type = 'info', ?string $link = null, ?string $icon = null): Notification
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'icon' => $icon,
            'link' => $link,
        ]);
    }

    /**
     * Notifikasi ke semua user dengan role tertentu
     */
    public function notifyRole(string $role, string $title, string $message, string $type = 'info', ?string $link = null): void
    {
        $users = User::where('role', $role)->where('is_active', true)->get();
        foreach ($users as $user) {
            $this->create($user->id, $title, $message, $type, $link);
        }
    }

    /**
     * Tandai semua notifikasi sebagai dibaca
     */
    public function markAllAsRead(int $userId): void
    {
        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Hitung notifikasi belum dibaca
     */
    public function unreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Ambil notifikasi terbaru
     */
    public function getLatest(int $userId, int $limit = 10)
    {
        return Notification::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->take($limit)
            ->get();
    }
}
