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

        $request = Http::withHeaders([
            'Authorization' => $this->token,
        ])->asForm();

        if (app()->environment('local')) {
            $request->withoutVerifying();
        }

        $response = $request->post('https://api.fonnte.com/send', [
            'target' => $this->normalizePhone($target),
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

    public function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        if (str_starts_with($phone, '+62')) {
            return substr($phone, 1);
        }

        if (str_starts_with($phone, '0')) {
            return '62'.substr($phone, 1);
        }

        return $phone;
    }
}
