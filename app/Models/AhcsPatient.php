<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * AhcsPatient
 *
 * Read-only model that connects to the external AHCS database.
 * Uses the 'ahcs' connection defined in config/database.php.
 * Maps to the `ahcs_patients` table on the remote AHCS server.
 *
 * The `patient_id` column in the local `users` table stores the
 * AHCS patient ID (ahcs_patients.id) to link a portal user to
 * their AHCS patient record.
 */
class AhcsPatient extends Model
{
    /**
     * Use the external AHCS database connection.
     */
    protected $connection = 'ahcs';

    /**
     * The AHCS patients table name.
     */
    protected $table = 'ahcs_patients';

    /**
     * The primary key for the AHCS patients table.
     */
    protected $primaryKey = 'id';

    /**
     * Disable timestamps if the AHCS table does not have them.
     * Set to true if ahcs_patients has created_at / updated_at.
     */
    public $timestamps = false;

    /**
     * Allow mass assignment for all columns (read-only model,
     * so mass assignment is safe here).
     */
    protected $guarded = [];

    // -------------------------------------------------------
    // Relationships back to the local admin panel database
    // -------------------------------------------------------

    /**
     * The portal user account linked to this AHCS patient.
     * Joins via users.patient_id = ahcs_patients.id
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'patient_id', 'id');
    }

    /**
     * All funnel assignments for this patient.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(PatientFunnelAssignment::class, 'patient_id', 'id');
    }

    /**
     * All form submissions for this patient.
     */
    public function formSubmissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class, 'patient_id', 'id');
    }

    /**
     * All funnel progress records for this patient.
     */
    public function funnelProgress(): HasMany
    {
        return $this->hasMany(FunnelProgress::class, 'patient_id', 'id');
    }

    /**
     * All messages for this patient.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'patient_id', 'id');
    }

    // -------------------------------------------------------
    // Accessor helpers
    // -------------------------------------------------------

    /**
     * Get the patient's full name.
     * Adjust field names to match your actual ahcs_patients columns.
     */
    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }
}
