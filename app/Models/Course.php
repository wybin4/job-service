<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'resume_id',
        'platform_name',
        'course_name',
        'description'
    ];

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }

}
