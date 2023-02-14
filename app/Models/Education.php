<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'resume_id',
        'university_name',
        'location',
        'speciality_name',
        'date_start',
        'date_end',
        'description'
    ];

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }

}
