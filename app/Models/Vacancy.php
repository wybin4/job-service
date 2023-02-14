<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'employer_id',
        'profession_id',
        'type_of_employment_id',
        'work_type_id',
        'salary',
        'work_experience',
        'location',
        'contacts',
        'description',
        'archived_at',
        'status'
    ];

    public function employer()
    {
        return $this->belongsTo(Employer::class);
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
    public function vacancy_skill()
    {
        return $this->hasMany(VacancySkill::class);
    }
    public function student_response()
    {
        return $this->hasMany(Interaction::class)->where('type', 0);
    }
    public function employer_offer()
    {
        return $this->hasMany(Interaction::class)->where('type', 1);
    }
}
