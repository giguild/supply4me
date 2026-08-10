<?php

namespace App\Services\Notification;

use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Send an email immediately.
     */
    public function send(string $to, string $subject, string $view, array $data = []): void
    {
        Mail::send($view, $data, function ($message) use ($to, $subject) {
            $message->to($to)
                ->subject($subject)
                ->from(config('mail.from.address'), config('mail.from.name'));
        });
    }

    /**
     * Queue an email for deferred sending.
     */
    public function queue(string $to, string $subject, string $view, array $data = []): void
    {
        SendEmailJob::dispatch($to, $subject, $view, $data);
    }
}
