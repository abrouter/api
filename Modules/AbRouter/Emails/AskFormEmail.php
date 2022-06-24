<?php

namespace Modules\AbRouter\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AskFormEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var
     */
    public $name;
    public $email;
    public $message;

    public function __construct($name, $email, $message)
    {
        $this->name = $name;
        $this->email = $email;
        $this->message = $message;
    }

    /**
     * @return AskFormEmail
     */
    public function build()
    {
        return $this->from($this->email)
            ->subject('Ask Form')
            ->view('abrouter::mail/askForm', [
                'name' => $this->name,
                'email' => $this->email,
                'test' => $this->message
            ]);
    }
}
