<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\AhcsPatient;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'patient_id',
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * The AHCS patient record linked to this user account.
     * patient_id stores the ahcs_patients.id from the external AHCS database.
     * Returns null for admin users who are not patients.
     */
    public function ahcsPatient(): BelongsTo
    {
        return $this->belongsTo(AhcsPatient::class, 'patient_id', 'id');
    }

    /**
     * Forms created by this admin user.
     */
    public function forms(): HasMany
    {
        return $this->hasMany(Form::class, 'created_by');
    }

    /**
     * Funnels created by this admin user.
     */
    public function funnels(): HasMany
    {
        return $this->hasMany(Funnel::class, 'created_by');
    }

    /**
     * Funnel assignments made by this admin user.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(PatientFunnelAssignment::class, 'assigned_by');
    }
}
