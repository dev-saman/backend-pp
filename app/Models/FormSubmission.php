<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id', 'patient_id', 'funnel_id', 'assignment_id',
        'patient_name', 'patient_email',
        'data', 'ip_address', 'user_agent', 'status',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function funnel()
    {
        return $this->belongsTo(Funnel::class);
    }

    public function assignment()
    {
        return $this->belongsTo(PatientFunnelAssignment::class, 'assignment_id');
    }
}
