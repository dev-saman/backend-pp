<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedhiwaSpeciality extends Model
{
    use HasFactory;
    protected $connection = 'medhiwa_ahcs';
    protected $table = 'med_speciality';
    protected $guarded = [];
}
