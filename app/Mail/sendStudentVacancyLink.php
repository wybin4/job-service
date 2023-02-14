<?php

namespace App\Mail;

use App\Models\Vacancy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class sendStudentVacancyLink extends Mailable
{
    use Queueable, SerializesModels;

    public $vacancy_id;
    public $profession_name;
    public function __construct($vacancy_id, $profession_name)
    {
        $this->vacancy_id = $vacancy_id;
        $this->profession_name = $profession_name;
    }

    public function build()
    {
        $vacancy = Vacancy::find($this->vacancy_id);
        return $this->subject(
            'Новая вакансия'
        )->markdown('student.vacancy.vacancy-link', [
            'url' => route('student.vacancy', ['id' => $this->vacancy_id]),
        ])->with('profession_name', $this->profession_name)
        ->with('vacancy', $vacancy);
    }
}
