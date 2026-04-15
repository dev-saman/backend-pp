<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedhiwaDuration extends Model
{
    use HasFactory;
    protected $connection = 'medhiwa_ahcs';
    protected $table = 'med_durations';
    protected $guarded = [];
}
