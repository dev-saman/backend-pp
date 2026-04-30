<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'fields', 'created_by', 'submission_count',
        'success_msg', 'thanks_msg',
        'assign_type', 'assign_user_id',
        'logo', 'bccemail', 'email', 'ccemail',
        'amount', 'currency_symbol', 'currency_name',
        'is_active', 'allow_share_section', 'allow_comments',
        'payment_status', 'payment_type', 'html',
    ];

    protected $casts = [
        'fields' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions()
    {
        return $this->hasMany(FormSubmission::class);
    }
}
