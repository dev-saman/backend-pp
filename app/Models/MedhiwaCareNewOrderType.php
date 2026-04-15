<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedhiwaCareNewOrderType extends Model
{
    use HasFactory;
    protected $connection = 'medhiwa_ahcs';
    protected $table = 'med_care_new_order_types';
    protected $guarded = [];
    
}
