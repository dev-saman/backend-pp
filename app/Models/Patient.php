<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'mrn', 'first_name', 'last_name', 'email', 'phone',
        'date_of_birth', 'gender', 'address', 'city', 'state',
        'zip_code', 'insurance_provider', 'insurance_member_id',
        'insurance_group_number', 'primary_physician', 'status', 'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    public function formSubmissions()
    {
        return $this->hasMany(FormSubmission::class);
    }

    // Alias used by analytics withCount('submissions')
    public function submissions()
    {
        return $this->hasMany(FormSubmission::class);
    }

    public function assignments()
    {
        return $this->hasMany(PatientFunnelAssignment::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public static function generateMrn(): string
    {
        $lastPatient = static::withTrashed()->orderBy('id', 'desc')->first();
        $nextId = $lastPatient ? $lastPatient->id + 1 : 1;
        return 'MRN' . str_pad($nextId, 7, '0', STR_PAD_LEFT);
    }
}
