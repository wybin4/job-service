<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VacancySkillRate extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'vacancy_id',
        'skill_id',
        'student_id',
        'skill_rate'
    ];

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }
    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
