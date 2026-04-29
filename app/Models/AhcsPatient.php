<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AhcsPatient extends Model
{
    use HasFactory;

    protected $connection = 'ahcs';
    protected $table = 'ahcs_patients';

    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $guarded = [];
}
