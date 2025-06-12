<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    protected $fillable = ['path', 'disk', 'is_primary'];

    // Relationship: Polymorphic
    public function imageable()
    {
        return $this->morphTo();
    }

    // Helper: Get full URL
    public function getUrlAttribute()
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    // Helper: Delete file when model is deleted
    protected static function booted()
    {
        static::deleted(function ($image) {
            Storage::disk($image->disk)->delete($image->path);
        });
    }
}
