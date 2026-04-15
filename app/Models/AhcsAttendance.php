<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AhcsAttendance extends Model
{
    use HasFactory;

    protected $table = 'ahcs_attendances';
    protected $connection = 'ahcs';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $guarded = [];
}
