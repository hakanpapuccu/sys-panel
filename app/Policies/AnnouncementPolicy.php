<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_announcements');
    }

    public function view(User $user, Announcement $announcement): bool
    {
        return $user->hasPermission('view_announcements');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_announcements');
    }

    public function update(User $user, Announcement $announcement): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $announcement->user_id === $user->id && $user->hasPermission('edit_announcements');
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $announcement->user_id === $user->id && $user->hasPermission('delete_announcements');
    }
}
