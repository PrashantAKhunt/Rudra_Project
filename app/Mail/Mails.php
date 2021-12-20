<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Mails extends Mailable {

    use Queueable,
        SerializesModels;

    public $mail_body, $subject,$mail_header,$mail_footer,$attach;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $mail_body,$attach = '') {
        $this->mail_body = $mail_body;
        $this->subject = $subject;
        $this->attach = $attach;
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        
        if ($this->attach) {
            return $this->from(config('app.FROM_EMAIL'))
                        ->subject($this->subject)
                        ->view('emails.generalmail')
                        ->attach($this->attach);
        } else {
            return $this->from(config('app.FROM_EMAIL'))
                        ->subject($this->subject)
                        ->view('emails.generalmail');
        }
        
    }

}
