<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubsphereOfActivity extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'sphere_id',
        'subsphere_of_activity_name',
    ];

    public function sphere_of_activity()
    {
        return $this->belongsTo(SphereOfActivity::class);
    }
}
