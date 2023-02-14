<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniversityLoginToken extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $dates = [
        'expires_at', 'consumed_at',
    ];

    public function university()
    {
        return $this->belongsTo(University::class);
    }
    public function university_id()
    {
        return $this->university->id;
    }

    public function isValid()
    {
        return !$this->isExpired() && !$this->isConsumed();
    }

    public function isExpired()
    {
        return $this->expires_at->isBefore(now());
    }

    public function isConsumed()
    {
        return $this->consumed_at !== null;
    }

    public function consume()
    {
        $this->consumed_at = now();
        $this->save();
    }
}
