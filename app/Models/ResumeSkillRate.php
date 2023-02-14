<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResumeSkillRate extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'student_skill_id',
        'employer_id',
        'skill_rate'
    ];

    public function student_skill()
    {
        return $this->belongsTo(StudentSkill::class);
    }
    public function resume()
    {
        return $this->student_skill()->resume();
    }
    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }
}
