<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSession extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'user_sessions';
    protected $guarded = [];
    public $timestamps = true;
}
