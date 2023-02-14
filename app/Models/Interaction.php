<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Interaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'vacancy_id',
        'student_id',
        'status',
        'type',
        'data'
    ];

    protected function data(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    } 
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }
}
