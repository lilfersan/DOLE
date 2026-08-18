<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryDuplicate extends Model
{
    protected $table = 'beneficiary_duplicates';

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
