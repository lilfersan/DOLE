<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    protected $fillable = [
        'name',
        'age',
        'id_number',
        'email',
        'birthdate',
        'employment_status',
        'student_status',
        'pwd_status',
        'eligibility_status',
        'is_eligible',
        'tags',
    ];
}
