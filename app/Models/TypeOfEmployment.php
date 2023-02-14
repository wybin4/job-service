<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeOfEmployment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'type_of_employment_name',
    ];
}
