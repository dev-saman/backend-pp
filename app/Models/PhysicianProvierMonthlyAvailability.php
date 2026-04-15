<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicianProvierMonthlyAvailability extends Model
{
    use HasFactory;
    protected $connection = 'physician';
    protected $table = 'provider_monthly_availability';
}
