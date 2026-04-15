<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicianAddress extends Model
{
    use HasFactory;
    protected $connection = 'physician';
    protected $table = 'physician_addresses';
    protected $guarded = [];
    public $timestamps = false;

    public function physician()
    {
        return $this->belongsTo(Physician::class, 'physician_id', 'id');
    }
}
