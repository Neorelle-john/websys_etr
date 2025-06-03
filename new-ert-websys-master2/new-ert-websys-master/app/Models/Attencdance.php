<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attencdance extends Model
{
    //

    protected $fillable =  [
        'employee_id',
        'date',
        'am_time_in',
        'am_time_out',
        'pm_time_in',
        'pm_time_out',
    ];

}
