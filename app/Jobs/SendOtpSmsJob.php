<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOtpSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $phone,
        private readonly string $otp,
        private readonly string $purpose = 'register'
    ) {}

    public function handle(): void
    {
        // TODO: integrate real SMS gateway (e.g. Sparrow SMS, Viber, etc.)
        Log::info('SendOtpSmsJob', [
            'phone'   => $this->phone,
            'otp'     => $this->otp,
            'purpose' => $this->purpose,
        ]);
    }
}
