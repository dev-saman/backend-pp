<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Physician extends Model
{
    use HasFactory;
    protected $connection = 'physician';
    protected $table = 'physicians';
    protected $guarded = [];
    public $timestamps = false;

    public function physicianAddresses()
    {
        return $this->hasMany(PhysicianAddress::class, 'physician_id','id')
                ->whereNull('deleted_at');
    }
}
