<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BlastMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectLine;
    public $bodyContent;

    public function __construct($subjectLine, $bodyContent)
    {
        $this->subjectLine = $subjectLine;
        $this->bodyContent = $bodyContent;
    }

    public function build()
    {
        // Gunakan view() untuk membungkus konten email ke dalam template blade
        return $this->subject($this->subjectLine)
            ->view('crm.email_blasts.blast_wrapper')
            ->with([
                'subjectLine' => $this->subjectLine,
                'bodyContent' => $this->bodyContent,
            ]);
    }
}
