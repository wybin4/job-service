<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployerQuality extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'quality_name',
    ];
    public function employer_rate()
    {
        return $this->hasMany(EmployerRate::class);
    }
}
