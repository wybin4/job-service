<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployerRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'employer_id',
        'quality_id',
        'student_id',
        'quality_rate',
    ];

    public function quality()
    {
        return $this->belongsTo(EmployerQuality::class);
    }

    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }
}
