<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkExperience extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'resume_id',
        'company_name',
        'location',
        'work_title',
        'date_start',
        'date_end', 
        'description'
    ];

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }

}
