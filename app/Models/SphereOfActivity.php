<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SphereOfActivity extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'sphere_of_activity_name',
    ];

    public function subsphere_of_activity()
    {
        return $this->hasMany(SubsphereOfActivity::class);
    }
}
