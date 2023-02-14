<?php

namespace App\Models;

use App\Mail\sendEmployerLoginLink;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class Employer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guard = 'employer';
    protected $fillable = [
        'name',
        'email',
        'password',
        'location',
        'description',
        'image',
    ];
    protected $hidden = [
        'password',
    ];

    public function loginTokens()
    {
        return $this->hasMany(EmployerLoginToken::class);
    }
    public function sendLoginLink()
    {
        $plaintext = Str::random(32);
        $token = $this->loginTokens()->create([
            'token' => hash('sha256', $plaintext),
            'expires_at' => now()->addMinutes(15),
        ]);
        Mail::to($this->email)->queue(new sendEmployerLoginLink($plaintext, $token->expires_at));
    }
    public function all_vacancy()
    {
        return $this->hasMany(Vacancy::class);
    }
    public function active_vacancy()
    {
        return $this->hasMany(Vacancy::class)->where('status', 0);
    }
    public function archived_vacancy()
    {
        return $this->hasMany(Vacancy::class)->where('status', 1);
    }
    public function employer_invitation()
    {
        return $this->hasMany(EmployerInvitation::class);
    }
    public function resume_skill_rate()
    {
        return $this->hasMany(ResumeSkillRate::class);
    }
}
