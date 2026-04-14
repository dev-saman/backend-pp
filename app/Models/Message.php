<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'parent_id')->orderBy('created_at');
    }

    public function parent()
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }
}
