<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'responses',
        'total_score',
        'risk_level',
        'recommendations',
        'completed_at'
    ];

    protected $casts = [
        'responses' => 'array',
        'total_score' => 'integer',
        'completed_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserApp::class, 'user_id');
    }

    public function scopeByRiskLevel($query, $riskLevel)
    {
        return $query->where('risk_level', $riskLevel);
    }

    public function scopeCompletedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('completed_at', [$startDate, $endDate]);
    }

    /**
     * Scope untuk filter berdasarkan fasilitas kesehatan
     */
    public function scopeByFacility($query, $facility)
    {
        return $query->whereHas('user', function ($q) use ($facility) {
            $q->where('fasilitas_kesehatan', $facility);
        });
    }

    /**
     * Get formatted completion date
     */
    public function getFormattedCompletedAtAttribute()
    {
        return $this->completed_at ? $this->completed_at->format('d/m/Y H:i') : null;
    }

    /**
     * Get risk level badge color
     */
    public function getRiskLevelColorAttribute()
    {
        return match($this->risk_level) {
            'Rendah' => 'success',
            'Sedang-Tinggi' => 'warning',
            default => 'gray'
        };
    }

    public static function determineRiskLevel($responses)
    {
        
        $hasRiskyAnswer = collect($responses)->contains(function ($response) {
            return $response['score'] == 1;
        });

        return $hasRiskyAnswer ? 'Sedang-Tinggi' : 'Rendah';
    }

    public static function generateRecommendations($riskLevel, $responses = [])
    {
        $recommendations = [];

       
        $recommendations[] = 'Lakukan deteksi dini kanker serviks melalui IVA di Puskesmas atau Pap Smear di Fasilitas Kesehatan lainnya untuk hasil yang lebih akurat.';

        if ($riskLevel === 'Rendah') {
            $recommendations[] = 'Tetap jaga pola hidup sehat dan kebersihan personal.';
            $recommendations[] = 'Lakukan pemeriksaan rutin sesuai jadwal.';
        } else {
            $recommendations[] = 'Segera konsultasi dengan dokter atau tenaga kesehatan terdekat.';
            $recommendations[] = 'Perbaiki pola hidup dan kebersihan personal.';
            $recommendations[] = 'Ikuti anjuran dan pengobatan dari tenaga kesehatan.';
        }

        return implode('; ', $recommendations);
    }

    public const RISK_LEVELS = [
        'Rendah' => 'Rendah',
        'Sedang-Tinggi' => 'Sedang-Tinggi'
    ];
}