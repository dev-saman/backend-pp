<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PatientFunnelAssignment extends Model
{
    protected $fillable = [
        'patient_id',
        'funnel_id',
        'assigned_by',
        'token',
        'status',
        'progress_percent',
        'forms_completed',
        'forms_total',
        'last_accessed_at',
        'completed_at',
        'expires_at',
        'note',
    ];

    protected $casts = [
        'last_accessed_at' => 'datetime',
        'completed_at'     => 'datetime',
        'expires_at'       => 'datetime',
        'progress_percent' => 'integer',
        'forms_completed'  => 'integer',
        'forms_total'      => 'integer',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function funnel(): BelongsTo
    {
        return $this->belongsTo(Funnel::class);
    }

    /**
     * The admin user who assigned this funnel.
     * Consistent user() alias used across all three tables.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * The admin user who assigned this funnel (explicit named relationship).
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(FunnelProgress::class, 'assignment_id');
    }

    /**
     * Get the patient's unique fill URL
     */
    public function getFillUrlAttribute(): string
    {
        return url('/fill/' . $this->token);
    }

    /**
     * Check if the assignment link has expired
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Recalculate and update progress based on funnel_progress records
     */
    public function recalculateProgress(): void
    {
        $formIds = $this->funnel->form_ids ?? [];
        $total   = count($formIds);

        if ($total === 0) return;

        $completed = $this->progress()->where('status', 'completed')->count();
        $percent   = (int) round(($completed / $total) * 100);

        $this->update([
            'forms_completed'  => $completed,
            'forms_total'      => $total,
            'progress_percent' => $percent,
            'status'           => $percent === 100 ? 'completed' : ($completed > 0 ? 'in_progress' : 'pending'),
            'completed_at'     => $percent === 100 ? now() : null,
        ]);
    }
}
