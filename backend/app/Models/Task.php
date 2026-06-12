<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $table = 'tasks';
    protected $primaryKey = 'task_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'task_id',
        'student_id',
        'task_name',
        'file_url',
        'status',
        'due_date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
}
