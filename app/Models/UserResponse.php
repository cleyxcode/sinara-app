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

    public static function determineRiskLevel($responses)
    {
        // Kotak merah adalah jawaban berisiko (score 1)
        $hasRiskyAnswer = collect($responses)->contains(function ($response) {
            return $response['score'] == 1;
        });

        return $hasRiskyAnswer ? 'Sedang-Tinggi' : 'Rendah';
    }

    public static function generateRecommendations($riskLevel, $responses = [])
    {
        $recommendations = [];

        // Rekomendasi umum untuk semua tingkat risiko
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