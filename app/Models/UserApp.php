<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserApp extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_apps';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'alamat',
        'umur',
        'phone',
        'fasilitas_kesehatan',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the responses for the user.
     * (Existing relationship - assuming this exists based on your UserAppResource)
     */
    public function responses()
    {
        return $this->hasMany(UserResponse::class, 'user_id');
    }

    /**
     * Get the IVA test results for the user.
     */
    public function ivaTestResults()
    {
        return $this->hasMany(IvaTestResult::class, 'user_id');
    }

    /**
     * Get the latest IVA test result for the user.
     */
    public function latestIvaTestResult()
    {
        return $this->hasOne(IvaTestResult::class, 'user_id')->latest('examination_date');
    }

    /**
     * Get positive IVA test results for the user.
     */
    public function positiveIvaResults()
    {
        return $this->hasMany(IvaTestResult::class, 'user_id')->where('result', 'positif');
    }

    /**
     * Get negative IVA test results for the user.
     */
    public function negativeIvaResults()
    {
        return $this->hasMany(IvaTestResult::class, 'user_id')->where('result', 'negatif');
    }

    /**
     * Check if user has any positive IVA test results.
     */
    public function hasPositiveIvaResult(): bool
    {
        return $this->positiveIvaResults()->exists();
    }

    /**
     * Check if user has recent IVA test (within last 6 months).
     */
    public function hasRecentIvaTest(): bool
    {
        return $this->ivaTestResults()
            ->where('examination_date', '>=', now()->subMonths(6))
            ->exists();
    }

    /**
     * Get total count of IVA tests for this user.
     */
    public function getTotalIvaTestsAttribute(): int
    {
        return $this->ivaTestResults()->count();
    }

    /**
     * Get count of positive IVA tests for this user.
     */
    public function getPositiveIvaTestsAttribute(): int
    {
        return $this->positiveIvaResults()->count();
    }

    /**
     * Get count of negative IVA tests for this user.
     */
    public function getNegativeIvaTestsAttribute(): int
    {
        return $this->negativeIvaResults()->count();
    }
}