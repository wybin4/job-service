<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'subsphere_id',
        'profession_name',
    ];

    public function subsphere_of_activity()
    {
        return $this->belongsTo(SubsphereOfActivity::class);
    }
}
