<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'order',
        'question_text',
        'options',
        'is_active'
    ];

    protected $casts = [
        'options' => 'array',
        'is_active' => 'boolean'
    ];

   
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

   
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

   
    public const CATEGORIES = [
        'Personal Hygiene' => 'Personal Hygiene',
        'Aktivitas Seksual' => 'Aktivitas Seksual', 
        'Ekonomi' => 'Ekonomi',
        'Gaya Hidup' => 'Gaya Hidup',
        'Riwayat Obstetri dan KB' => 'Riwayat Obstetri dan KB',
        'Riwayat Penyakit' => 'Riwayat Penyakit',
        'Riwayat Skrining' => 'Riwayat Skrining'
    ];
}