<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class sendOfferLink extends Mailable
{
    use Queueable, SerializesModels;

    public $topic;
    public $text;
    public $vacancy_id;

    public function __construct($vacancy_id, $topic, $text)
    {
        $this->vacancy_id = $vacancy_id;
        $this->topic = $topic;
        $this->text = $text;
    }

    public function build()
    {
        return $this->subject($this->topic)->markdown('employer.offer-link', [
            'url' => url('student/vacancy/' . $this->vacancy_id),
        ])->with('text', $this->text);
    }
}
