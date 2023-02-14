<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployerNotification extends Notification
{
    use Queueable;

    public $type;
    public $student_id;
    public $vacancy_id;
    public function __construct($type, $student_id, $vacancy_id)
    {
        $this->type = $type;
        $this->student_id = $student_id;
        $this->vacancy_id = $vacancy_id;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => $this->type,
            'student_id' => $this->student_id,
            'vacancy_id' => $this->vacancy_id
        ];
    }

}
