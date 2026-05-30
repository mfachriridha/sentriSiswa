<?php

namespace App\Jobs;

use App\Models\WhatsappMessage;
use App\Services\FonnteService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Facades\Log;

class SendWhatsAppNotification implements ShouldQueue
{
    use Queueable;

    public int $timeout = 30;

    public function __construct(
        public WhatsappMessage $whatsappMessage,
    ) {}

    public function handle(FonnteService $fonnte): void
    {
        $this->whatsappMessage->update(['status' => 'processing']);

        $result = $fonnte->send(
            $this->whatsappMessage->recipient_phone,
            $this->whatsappMessage->message,
        );

        if ($result['success']) {
            $this->whatsappMessage->update([
                'status' => 'sent',
                'response' => json_encode($result['response']),
                'sent_at' => now(),
            ]);
        } else {
            $this->whatsappMessage->update([
                'status' => 'failed',
                'response' => json_encode($result['response'] ?? $result['error']),
            ]);

            Log::warning('Gagal kirim WA ke '.$this->whatsappMessage->recipient_phone.': '.($result['error'] ?? ''));
        }
    }

    public function middleware(): array
    {
        return [(new RateLimited('whatsapp'))->dontRelease()];
    }

    public function failed(?\Throwable $e): void
    {
        $this->whatsappMessage->update([
            'status' => 'failed',
            'response' => $e?->getMessage(),
        ]);
    }
}
