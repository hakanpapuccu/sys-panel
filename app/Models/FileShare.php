<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileShare extends Model
{
    protected $table = 'files';

    protected $fillable = [
        'user_id',
        'folder_id',
        'title',
        'description',
        'file_path',
        'storage_disk',
        'file_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }
}
