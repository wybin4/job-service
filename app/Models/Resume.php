<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'student_id',
        'profession_id',
        'type_of_employment_id',
        'work_type_id',
        'about_me',
        'archived_at',
        'status'
    ];
 
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }
    public function type_of_employment()
    {
        return $this->belongsTo(TypeOfEmployment::class);
    }
    public function work_type()
    {
        return $this->belongsTo(WorkType::class);
    }
    public function work_experience()
    {
        return $this->hasMany(WorkExperience::class);
    }
    public function education()
    {
        return $this->hasMany(Education::class);
    }
    public function course()
    {
        return $this->hasMany(Course::class);
    }
    public function student_skill()
    {
        return $this->hasMany(StudentSkill::class);
    }
    public function employer_invitation()
    {
        return $this->hasMany(EmployerInvitation::class);
    }
}
