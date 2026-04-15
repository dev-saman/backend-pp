<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicianCustomLunchTime extends Model
{
    use HasFactory;
    protected $connection = 'physician';
    protected $table = 'physician_custom_lunch_times';
    protected $guarded = [];

}
