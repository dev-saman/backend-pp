<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AhcsPatient;

class FunnelProgress extends Model
{
    protected $table = 'funnel_progress';

    protected $fillable = [
        'user_id',
        'assignment_id',
        'patient_id',
        'funnel_id',
        'form_id',
        'step_index',
        'status',
        'data',
        'last_saved_at',
        'submitted_at',
    ];

    protected $casts = [
        'data'          => 'array',
        'last_saved_at' => 'datetime',
        'submitted_at'  => 'datetime',
        'step_index'    => 'integer',
    ];

    /**
     * The admin user who owns / initiated this progress record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(PatientFunnelAssignment::class, 'assignment_id');
    }

    /**
     * The AHCS patient this progress record belongs to.
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

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
