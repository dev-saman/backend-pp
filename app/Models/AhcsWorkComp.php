<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AhcsWorkComp extends Model
{
    use HasFactory;
    protected $table = 'ahcs_work_comps';
    protected $connection = 'ahcs';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;

    protected $guarded = [];
}
