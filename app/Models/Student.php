<?php

namespace App\Models;

use App\Mail\sendStudentLoginLink;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class Student extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guard = 'student';
    protected $fillable = [
        'student_fio',
        'university_id',
        'email',
        'password',
        'location',
        'image',
    ];
    protected $hidden = [
        'password',
    ];
    public function loginTokens()
    {
        return $this->hasMany(StudentLoginToken::class);
    }
    public function resume()
    {
        return $this->hasOne(Resume::class)->where('status', 0);
    }
    public function archived_resumes()
    {
        return $this->hasMany(Resume::class)->where('status', 1);
    }
    public function student_response()
    {
        return $this->hasMany(StudentResponse::class)->where('type', 0);
    }
    public function sendLoginLink()
    {
        $plaintext = Str::random(32);
        $token = $this->loginTokens()->create([
            'token' => hash('sha256', $plaintext),
            'expires_at' => now()->addMinutes(15),
        ]);
        Mail::to($this->email)->queue(new sendStudentLoginLink($plaintext, $token->expires_at));
    }
}
