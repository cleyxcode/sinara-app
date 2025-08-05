<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'image',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    /**
     * Scope untuk artikel yang dipublish
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return Storage::url($this->image);
        }
        return null;
    }

   
    public function getExcerptAttribute()
    {
        $length = 150;
        
      
        $plainText = strip_tags($this->content);
        
        
        if (strlen($plainText) <= $length) {
            return trim($plainText);
        }
        
      
        $excerpt = substr($plainText, 0, $length);
        $lastSpace = strrpos($excerpt, ' ');
        
        if ($lastSpace !== false) {
            $excerpt = substr($excerpt, 0, $lastSpace);
        }
        
        return trim($excerpt) . '...';
    }
}