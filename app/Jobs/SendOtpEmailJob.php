<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendOtpEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 30;

    public function __construct(
        private readonly string $email,
        private readonly string $otp,
        private readonly string $purpose = 'register'
    ) {}

    public function handle(): void
    {
        $subject = match ($this->purpose) {
            'reset_password' => 'Reset Your Password — Nagarik+',
            'reset_pin'      => 'Reset Your PIN — Nagarik+',
            'register'       => 'Verify Your Email — Nagarik+',
            default          => 'Your OTP Code — Nagarik+',
        };

        $purposeLabel = match ($this->purpose) {
            'reset_password' => 'reset your password',
            'reset_pin'      => 'reset your PIN',
            'register'       => 'verify your email',
            default          => 'complete your request',
        };

        $html = $this->buildHtml($this->otp, $purposeLabel);

        try {
            Mail::html($html, function ($message) use ($subject) {
                $message
                    ->to($this->email)
                    ->subject($subject)
                    ->from(
                        config('mail.from.address'),
                        config('mail.from.name')
                    );
            });

            Log::info('OTP email sent', [
                'email'   => $this->email,
                'purpose' => $this->purpose,
            ]);
        } catch (\Throwable $e) {
            Log::error('OTP email failed', [
                'email'   => $this->email,
                'purpose' => $this->purpose,
                'error'   => $e->getMessage(),
            ]);
            throw $e; // let the queue retry
        }
    }

    private function buildHtml(string $otp, string $purposeLabel): string
    {
        $appName = config('app.name', 'Nagarik+');
        $year    = date('Y');

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OTP Code</title>
</head>
<body style="margin:0;padding:0;background-color:#F2F5FA;font-family:'Segoe UI',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#F2F5FA;padding:40px 0;">
    <tr>
      <td align="center">
        <table width="480" cellpadding="0" cellspacing="0"
               style="background:#ffffff;border-radius:16px;overflow:hidden;
                      box-shadow:0 4px 24px rgba(0,0,0,0.08);">

          <!-- Header -->
          <tr>
            <td style="background:linear-gradient(135deg,#1565C0,#0D47A1);
                       padding:32px 40px;text-align:center;">
              <h1 style="margin:0;color:#ffffff;font-size:26px;font-weight:800;
                         letter-spacing:-0.5px;">{$appName}</h1>
              <p style="margin:6px 0 0;color:rgba(255,255,255,0.75);font-size:13px;">
                Digital Government Services
              </p>
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td style="padding:40px 40px 32px;">
              <p style="margin:0 0 8px;font-size:15px;color:#1A2B4A;font-weight:600;">
                Hello,
              </p>
              <p style="margin:0 0 28px;font-size:14px;color:#4A5568;line-height:1.6;">
                You requested to {$purposeLabel}. Use the OTP code below.
                This code expires in <strong>10 minutes</strong>.
              </p>

              <!-- OTP Box -->
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td align="center">
                    <div style="background:#F0F4FF;border:2px dashed #1565C0;
                                border-radius:12px;padding:24px 20px;
                                display:inline-block;margin:0 auto;">
                      <p style="margin:0 0 6px;font-size:12px;color:#1565C0;
                                 font-weight:700;letter-spacing:2px;text-transform:uppercase;">
                        Your OTP Code
                      </p>
                      <p style="margin:0;font-size:42px;font-weight:900;
                                 color:#1565C0;letter-spacing:12px;
                                 font-family:'Courier New',monospace;">
                        {$otp}
                      </p>
                    </div>
                  </td>
                </tr>
              </table>

              <p style="margin:28px 0 0;font-size:13px;color:#718096;line-height:1.6;">
                If you did not request this, please ignore this email.
                Your account remains secure.
              </p>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="background:#F8FAFC;padding:20px 40px;
                       border-top:1px solid #E8EEF7;text-align:center;">
              <p style="margin:0;font-size:12px;color:#A0AEC0;">
                &copy; {$year} {$appName} &mdash; Nepal Digital Services<br>
                This is an automated email. Please do not reply.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;
    }
}
