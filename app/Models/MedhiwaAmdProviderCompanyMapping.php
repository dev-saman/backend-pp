<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedhiwaAmdProviderCompanyMapping extends Model
{
    use HasFactory;
    protected $connection = 'medhiwa_ahcs';
    protected $table = 'med_amd_provider_company_mapping';
    protected $guarded = [];

}
