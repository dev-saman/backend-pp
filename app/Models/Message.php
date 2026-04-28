<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\AhcsPatient;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'admin_id', 'subject', 'body', 'direction',
        'status', 'category', 'has_attachment', 'sender_name', 'sender_type',
        'is_read', 'parent_id',
    ];

    protected $casts = [
        'has_attachment' => 'boolean',
        'is_read'        => 'boolean',
    ];

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    /**
     * The AHCS patient this message belongs to.
     * patient_id references ahcs_patients.id on the external AHCS database.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(AhcsPatient::class, 'patient_id', 'id');
    }

    /**
     * The admin user who sent / manages this message.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Child reply messages in the same thread.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Message::class, 'parent_id')->orderBy('created_at');
    }

    /**
     * The parent message this is a reply to.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }
}
