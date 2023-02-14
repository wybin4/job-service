<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VacancySkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'vacancy_id',
        'skill_id',
    ];

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }
}
