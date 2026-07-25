<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegisterCourse extends Model
{
    protected $table = 'register_course';

    protected $fillable = [
        'student_id',
        'course_id',
        'name',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'high_school',
        'high_school_pass_year',
        'primary_school',
        'primary_school_pass_year',
        'employer',
        'active_years',
        'vocational_school',
        'heard_about_us',
        'signature_path',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
