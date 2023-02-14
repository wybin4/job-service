<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployerLoginToken extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $dates = [
        'expires_at', 'consumed_at',
    ];

    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }
    public function employer_id()
    {
        return $this->employer->id;
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
