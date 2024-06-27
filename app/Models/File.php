<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($file) {
            // Delete the file
            if ($file->path) {
                Storage::disk('private')->delete($file->path);
            }
        });

        static::updating(function ($file) {
            // Delete the file
            if ($file->isdirty()) {
                $originalFile = $file->getOriginal('path');
                if ($originalFile) {
                    Storage::disk('private')->delete($originalFile);
                }
            }
        });
    }
}
