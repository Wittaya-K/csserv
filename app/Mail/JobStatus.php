<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobStatus extends Mailable
{
    use Queueable, SerializesModels;

    public $details;
    public $emails;
    public $filenames;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details,$emails,$filenames)
    {
        $this->details = $details;
        $this->emails  = $emails;
        $this->filenames = $filenames;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $emails = $this->emails;
        return $this->from('wittaya.kh@psu.ac.th', 'ระบบแจ้งขอใช้บริการ')
                    ->subject('ระบบแจ้งขอใช้บริการ')
                    ->to($emails)
                    ->view('emails.JobStatus')
                    ->with('details', $this->details)
                    ->with('filenames',$this->filenames);
    }
}

