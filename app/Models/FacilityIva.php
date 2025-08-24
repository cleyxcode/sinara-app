<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityIva extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'location',
        'address',
        'latitude',
        'longitude',
        'phone',
        'iva_training_years',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'iva_training_years' => 'array',
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    /**
     * Scope untuk fasilitas aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk filter berdasarkan lokasi
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', 'LIKE', '%' . $location . '%');
    }

    /**
     * Scope untuk fasilitas yang memiliki koordinat
     */
    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('latitude')
                    ->whereNotNull('longitude');
    }

    /**
     * Hitung jarak antara dua koordinat (Haversine formula)
     * @param float $lat1 Latitude point 1
     * @param float $lon1 Longitude point 1
     * @param float $lat2 Latitude point 2
     * @param float $lon2 Longitude point 2
     * @return float Distance in kilometers
     */
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth radius in kilometers

        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLon = deg2rad($lon2 - $lon1);

        $a = sin($deltaLat/2) * sin($deltaLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($deltaLon/2) * sin($deltaLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    /**
     * Scope untuk mencari fasilitas terdekat berdasarkan koordinat
     */
    public function scopeNearBy($query, $latitude, $longitude, $radiusKm = 50)
    {
        return $query->selectRaw(
            "*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
            [$latitude, $longitude, $latitude]
        )
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->having('distance', '<=', $radiusKm)
        ->orderBy('distance');
    }

    /**
     * Get formatted training years
     */
    public function getFormattedTrainingYearsAttribute()
    {
        if (empty($this->iva_training_years)) {
            return 'Belum ada data pelatihan';
        }
        
        return implode(', ', $this->iva_training_years);
    }

    /**
     * Get full address with location
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->location
        ]);
        
        return implode(', ', $parts);
    }

    /**
     * Check if facility has coordinates
     */
    public function hasCoordinates()
    {
        return $this->latitude && $this->longitude;
    }

    /**
     * Konstanta lokasi yang tersedia
     */
    public const AVAILABLE_LOCATIONS = [
        'Kab. Kepulauan Tanimbar',
        'Kab. Maluku Tenggara',
        'Kab. Maluku Tengah',
        'Kab. Buru',
        'Kab. Buru Selatan',
        'Kab. Maluku Barat Daya',
        'Kab. Seram Bagian Barat',
        'Kab. Seram Bagian Timur',
        'Kab. Kepulauan Aru',
        'Kota Ambon',
        'Kota Tual'
    ];
}