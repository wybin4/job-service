<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSkill extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'resume_id',
        'skill_id',
        'skill_rate'
    ];

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }

    public function resume_skill_rate()
    {
        return $this->hasOne(ResumeSkillRate::class);
    }

}
