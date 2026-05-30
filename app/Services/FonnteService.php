<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class FonnteService
{
    private ?string $token;

    public function __construct()
    {
        $this->token = Setting::get('fonnte_token');
    }

    public function isConfigured(): bool
    {
        return filled($this->token);
    }

    public function send(string $target, string $message): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'Fonnte token belum dikonfigurasi.',
            ];
        }

        $response = Http::withHeaders([
            'Authorization' => $this->token,
        ])->asForm()->post('https://api.fonnte.com/send', [
            'target' => $target,
            'message' => $message,
        ]);

        $body = $response->json();

        if ($response->successful() && ($body['status'] ?? false)) {
            return [
                'success' => true,
                'response' => $body,
            ];
        }

        return [
            'success' => false,
            'error' => $body['reason'] ?? 'Gagal mengirim pesan WhatsApp.',
            'response' => $body,
        ];
    }
}
