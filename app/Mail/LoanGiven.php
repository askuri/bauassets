<?php

namespace App\Mail;

use App\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoanGiven extends Mailable
{
    use Queueable, SerializesModels;
    
    /**
     * This gets passed automatically to the email view.
     * @var App\Loan
     */
    public $loan;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Leihgabe erhalten / Loan obtained')
                ->markdown('emails.LoanGiven');
    }
}
