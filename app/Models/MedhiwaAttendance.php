<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedhiwaAttendance extends Model
{
    use HasFactory;
    protected $connection = 'patient_portal';
    protected $table = 'patient_attendances';
    protected $guarded = [];
    public $timestamps = false;
}
