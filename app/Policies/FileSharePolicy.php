<?php

namespace App\Policies;

use App\Models\FileShare;
use App\Models\User;

class FileSharePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_files');
    }

    public function view(User $user, FileShare $fileShare): bool
    {
        return $user->is_admin || $fileShare->user_id === $user->id;
    }

    public function download(User $user, FileShare $fileShare): bool
    {
        return $this->view($user, $fileShare);
    }

    public function preview(User $user, FileShare $fileShare): bool
    {
        return $this->view($user, $fileShare);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('upload_files');
    }

    public function createFolder(User $user): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, FileShare $fileShare): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $fileShare->user_id === $user->id && $user->hasPermission('delete_files');
    }

    public function move(User $user, FileShare $fileShare): bool
    {
        return $user->is_admin && $user->hasPermission('delete_files');
    }
}
