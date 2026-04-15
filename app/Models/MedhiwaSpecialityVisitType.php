<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class MedhiwaSpecialityVisitType extends Model
{
    use HasFactory;
    protected $connection = 'medhiwa_ahcs';
    protected $table = 'med_speciality_visittypes';
    protected $guarded = [];

    public function orderType()
    {
        return $this->belongsTo(MedhiwaCareNewOrderType::class, 'visittype_id', 'id');
    }

    public function duration()
    {
        return $this->belongsTo(MedhiwaDuration::class, 'duration_id', 'id');
    }
}
