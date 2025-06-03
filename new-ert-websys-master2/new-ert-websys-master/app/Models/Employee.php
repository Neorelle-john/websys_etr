<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    //
    protected $fillable = [
        'name',
        'id_number',
        'picture',
        'college',
        'classification',
    ];
}
