<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentRequested extends Mailable
{
    use Queueable, SerializesModels;

    public $pointRequest;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pointRequest)
    {
        $this->pointRequest = $pointRequest;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('B-LIVE')
            ->view('emails.payment_requested');
    }
}
