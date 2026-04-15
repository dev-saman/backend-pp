<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AhcsMedAuth extends Model
{
    use HasFactory;

    protected $table = 'ahcs_med_auths';
    protected $connection = 'ahcs';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $guarded = [];
}
