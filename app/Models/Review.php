<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $fillable = [
        'entity_id',
        'reviewer_id',
        'text',
        'type'
    ];
}
