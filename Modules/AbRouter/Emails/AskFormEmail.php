<?php
declare(strict_types=1);

namespace Modules\AbRouter\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AskFormEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;
    public string $email;
    public string $message;

    public function __construct($name, $email, $message)
    {
        $this->name = $name;
        $this->email = $email;
        $this->message = $message;
    }

    public function build(): AskFormEmail
    {
        return $this->subject('Ask Form')
            ->markdown('abrouter::mail/askForm', [
                'name' => $this->name,
                'email' => $this->email,
                'message' => $this->message
            ]);
    }
}
