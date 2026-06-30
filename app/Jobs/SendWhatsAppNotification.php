<?php

namespace App\Jobs;

use App\Models\PesanWhatsapp;
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
        public PesanWhatsapp $whatsappMessage,
    ) {}

    public function handle(FonnteService $whatsapp): void
    {
        $this->whatsappMessage->update(['status' => 'processing']);

        $result = $whatsapp->send(
            $this->whatsappMessage->telepon_penerima,
            $this->whatsappMessage->isi_pesan,
        );

        if ($result['success']) {
            $this->whatsappMessage->update([
                'status' => 'sent',
                'id_pesan_provider' => data_get($result, 'response.id.0'),
                'respons' => json_encode($result['response']),
                'dikirim_pada' => now(),
            ]);
        } else {
            $this->whatsappMessage->update([
                'status' => 'failed',
                'respons' => json_encode($result['response'] ?? $result['error']),
            ]);

            Log::warning('Gagal kirim WA ke '.$this->whatsappMessage->telepon_penerima.': '.($result['error'] ?? ''));
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
