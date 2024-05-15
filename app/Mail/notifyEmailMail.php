<?php

namespace App\Mail;

use http\Client\Curl\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class notifyEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $setting;
    public $quote;
    public $template;
    public $staff;
    public function __construct($quote,$setting,$template,$staff)
    {
        $this->quote = $quote;
        $this->setting = $setting;
        $this->template = $template;
        $this->staff = $staff;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $setting = $this->setting;
        $quote = $this->quote;
        $template = $this->template;
        $staff = $this->staff;

        $data2 = array(
            'setting'=>$setting,
            'quote'=>$quote,
            'template'=>$template,
            'staff'=>$staff,
        );
        return $this->subject("Notification Email")->view('emails.notifyEmail')->with($data2);
    }
}
