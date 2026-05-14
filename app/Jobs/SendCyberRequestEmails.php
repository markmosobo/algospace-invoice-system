<?php

namespace App\Jobs;

use App\Mail\CyberRequestReceived;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCyberRequestEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $cyberRequest;

    public function __construct($cyberRequest)
    {
        $this->cyberRequest = $cyberRequest;
    }

    public function handle()
    {
        // user email
        Mail::to($this->cyberRequest->email)
            ->send(new CyberRequestReceived($this->cyberRequest));

        // admin email
        Mail::to('info@algospace.co.ke')
            ->send(new CyberRequestReceived($this->cyberRequest));
    }
}