<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicianSpeciality extends Model
{
    use HasFactory;
    protected $connection = 'physician';
    protected $table = 'physician_specialties';
    protected $guarded = [];
    public $timestamps = false;
}
