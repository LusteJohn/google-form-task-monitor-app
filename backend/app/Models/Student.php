<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students';
    protected $primaryKey = 'student_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'student_id',
        'name',
        'email',
        'phone_number',
        'address',
    ];

}
