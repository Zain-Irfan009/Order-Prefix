<?php

namespace App\Mail;

use http\Client\Curl\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class invoiceEmailMail extends Mailable
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
    public function __construct($quote,$setting,$template)
    {
        $this->quote = $quote;
        $this->setting = $setting;
        $this->template = $template;
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

        $data2 = array(
            'setting'=>$setting,
            'quote'=>$quote,
            'template'=>$template,
        );
        return $this->subject("Thanks for Quote")->view('emails.invoiceEmail')->with($data2);
    }
}
