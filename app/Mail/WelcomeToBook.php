<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WelcomeToBook extends Mailable
{
    use Queueable, SerializesModels;

    public $m3_email;
    public $subject = '账号激活-优客书店';
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($m3_email)
    {
        $this->m3_email = $m3_email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('Email.book');
    }
}
