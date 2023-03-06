<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\sendLoginLink;
use App\Mail\sendUniversityLoginLink;

class University extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guard = 'university';
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    protected $hidden = [
        'password',
    ];
    public function students()
    {
        return $this->hasMany(Student::class);
    }
    public function loginTokens()
    {
        return $this->hasMany(UniversityLoginToken::class);
    }
    public function sendLoginLink()
    {
        $plaintext = Str::random(32);
        $token = $this->loginTokens()->create([
            'token' => hash('sha256', $plaintext),
            'expires_at' => now()->addMinutes(15),
        ]);
        Mail::to($this->email)->queue(new sendUniversityLoginLink($plaintext, $token->expires_at));
    }
}
