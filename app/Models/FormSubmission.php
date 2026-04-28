<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AhcsPatient;

class FormSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'form_id', 'patient_id', 'funnel_id', 'assignment_id',
        'patient_name', 'patient_email',
        'data', 'ip_address', 'user_agent', 'status',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * The admin user who created / owns this submission.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * The AHCS patient who submitted this form.
     * patient_id references ahcs_patients.id on the external AHCS database.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(AhcsPatient::class, 'patient_id', 'id');
    }

    public function funnel(): BelongsTo
    {
        return $this->belongsTo(Funnel::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(PatientFunnelAssignment::class, 'assignment_id');
    }
}
