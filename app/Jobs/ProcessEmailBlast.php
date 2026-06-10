<?php

namespace App\Jobs;

use App\Models\CrmEmailBlast;
use App\Models\CrmEmailBlastRecipient;
use App\Mail\BlastMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ProcessEmailBlast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $blast;

    // timeout for 1 hour so the worker is not killed by the system when blasting thousands of emails
    public $timeout = 3600;

    public function __construct(CrmEmailBlast $blast)
    {
        $this->blast = $blast;
    }

    public function handle()
    {
        $this->blast->update(['status' => 'processing']);

        $recipients = CrmEmailBlastRecipient::with('contact')
            ->where('email_blast_id', $this->blast->id)
            ->where('status', 'pending')
            ->get();

        $sentCount = $this->blast->sent_count;

        foreach ($recipients as $recipient) {
            $contact = $recipient->contact;

            if (!$contact) {
                $recipient->update(['status' => 'failed', 'error_message' => 'Contact not found or deleted.']);
                continue;
            }

            if (!filter_var($contact->email, FILTER_VALIDATE_EMAIL)) {
                $recipient->update([
                    'status' => 'failed',
                    'error_message' => 'Invalid email format.',
                ]);
                continue;
            }

            $domain = substr(strrchr($contact->email, '@'), 1);
            try {
                // dns validation layer so invalid emails are immediately rejected
                if (!checkdnsrr($domain, 'MX') && !checkdnsrr($domain, 'A')) {
                    $recipient->update([
                        'status' => 'failed',
                        'error_message' => "Target domain ($domain) not found or inactive.",
                    ]);
                    continue;
                }
            } catch (\Exception $e) {
                $recipient->update([
                    'status' => 'failed',
                    'error_message' => "Failed to verify domain ($domain).",
                ]);
                continue;
            }

            $body = $this->blast->body;
            $body = str_replace('[nama]', $contact->company_name ?? 'Customer', $body);
            $body = str_replace('[email]', $contact->email, $body);
            $body = str_replace('[perusahaan]', $contact->company_name ?? 'Your Company', $body);
            $body = str_replace('[telepon]', $contact->phone ?? '-', $body);

            try {
                // execute sending email to smtp
                Mail::to($contact->email)->send(new BlastMail($this->blast->subject, $body));
                $sentCount++;

                $recipient->update(['status' => 'sent']);

                // update ui in real-time per 5 emails
                if ($sentCount % 5 == 0) {
                    $this->blast->update(['sent_count' => $sentCount]);
                }

                // delay for 2 seconds after completing 1 email
                sleep(2);

                // extra delay (batch throttling):
                // every multiple of 10 sent emails, the worker rests for 10 seconds
                if ($sentCount % 10 == 0) {
                    sleep(10);
                }
            } catch (\Exception $e) {
                Log::error("Failed to send blast to {$contact->email}: " . $e->getMessage());

                $recipient->update([
                    'status' => 'failed',
                    'error_message' => substr($e->getMessage(), 0, 255),
                ]);

                // if error occurs (usually due to dropped smtp), pause longer so the smtp server resets connection
                sleep(5);
            }
        }

        // if all pending queues are empty, mark as completed
        $this->blast->update([
            'status' => 'completed',
            'sent_count' => $sentCount,
        ]);
    }
}
