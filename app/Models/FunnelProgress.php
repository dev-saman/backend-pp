<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FunnelProgress extends Model
{
    protected $table = 'funnel_progress';

    protected $fillable = [
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

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(PatientFunnelAssignment::class, 'assignment_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
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
