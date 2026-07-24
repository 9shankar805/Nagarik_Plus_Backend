<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOtpEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $email,
        private readonly string $otp,
        private readonly string $purpose = 'register'
    ) {}

    public function handle(): void
    {
        // TODO: integrate real email sending (Mail::to($this->email)->send(...))
        Log::info('SendOtpEmailJob', [
            'email'   => $this->email,
            'otp'     => $this->otp,
            'purpose' => $this->purpose,
        ]);
    }
}
