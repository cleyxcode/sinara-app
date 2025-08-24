<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class IvaTestResult extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'iva_test_results';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'examination_date',
        'examination_type',
        'result',
        'notes',
        'examined_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'examination_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the test result.
     */
    public function user()
    {
        return $this->belongsTo(UserApp::class, 'user_id');
    }

    /**
     * Scope a query to only include positive results.
     */
    public function scopePositive($query)
    {
        return $query->where('result', 'positif');
    }

    /**
     * Scope a query to only include negative results.
     */
    public function scopeNegative($query)
    {
        return $query->where('result', 'negatif');
    }

    /**
     * Scope a query to filter by examination type.
     */
    public function scopeByExaminationType($query, $type)
    {
        return $query->where('examination_type', $type);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate = null)
    {
        $query->whereDate('examination_date', '>=', $startDate);
        
        if ($endDate) {
            $query->whereDate('examination_date', '<=', $endDate);
        }
        
        return $query;
    }

    /**
     * Scope a query to filter by health facility.
     */
    public function scopeByHealthFacility($query, $facilityName)
    {
        return $query->whereHas('user', function ($q) use ($facilityName) {
            $q->where('fasilitas_kesehatan', $facilityName);
        });
    }

    /**
     * Get formatted examination date.
     */
    public function getFormattedExaminationDateAttribute()
    {
        return $this->examination_date->format('d/m/Y');
    }

    /**
     * Get result in Indonesian.
     */
    public function getResultInIndonesianAttribute()
    {
        return $this->result === 'positif' ? 'Positif' : 'Negatif';
    }

    /**
     * Get result badge color for UI.
     */
    public function getResultColorAttribute()
    {
        return $this->result === 'positif' ? 'danger' : 'success';
    }
}