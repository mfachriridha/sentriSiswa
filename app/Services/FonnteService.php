<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Throwable;

class FonnteService
{
    public const TEST_COOLDOWN_SECONDS = 60;

    private const ENDPOINT = 'https://api.fonnte.com/send';

    private const TIMEOUT_SECONDS = 60;

    private const CONNECT_TIMEOUT_SECONDS = 10;

    private const DELAY_MIN_SECONDS = 8;

    private const DELAY_MAX_SECONDS = 15;

    public function isConfigured(): bool
    {
        return filled($this->token());
    }

    /**
     * @param  array{timeout?: int, connect_timeout?: int, retries?: int}  $options
     * @return array{success: bool, error?: string, response?: mixed}
     */
    public function send(string $target, string $message, array $options = []): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'Token Fonnte belum dikonfigurasi. Isi token di pengaturan WhatsApp.',
            ];
        }

        $timeout = max(5, (int) ($options['timeout'] ?? self::TIMEOUT_SECONDS));
        $connectTimeout = max(3, (int) ($options['connect_timeout'] ?? self::CONNECT_TIMEOUT_SECONDS));
        $retries = max(0, (int) ($options['retries'] ?? 1));

        $request = Http::asMultipart()
            ->connectTimeout($connectTimeout)
            ->timeout($timeout)
            ->retry($retries, 500)
            ->withHeader('Authorization', $this->token());

        if (app()->environment('local')) {
            $request->withoutVerifying();
        }

        try {
            $response = $request->post(self::ENDPOINT, [
                'target' => $this->normalizePhone($target),
                'message' => $message,
                'countryCode' => '62',
            ]);
        } catch (ConnectionException) {
            return [
                'success' => false,
                'error' => 'Koneksi ke Fonnte timeout atau tidak bisa dijangkau.',
            ];
        } catch (Throwable) {
            return [
                'success' => false,
                'error' => 'Terjadi kesalahan saat menghubungi Fonnte.',
            ];
        }

        $body = $response->json();

        if (! is_array($body)) {
            return [
                'success' => false,
                'error' => 'Fonnte mengembalikan respons yang tidak valid.',
                'response' => $response->body(),
            ];
        }

        if ($response->successful() && ($body['status'] ?? false) === true) {
            return [
                'success' => true,
                'response' => $body,
            ];
        }

        return [
            'success' => false,
            'error' => $body['reason'] ?? 'Gagal mengirim pesan WhatsApp melalui Fonnte.',
            'response' => $body,
        ];
    }

    public function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone) ?? '';

        if (str_starts_with($phone, '+62')) {
            return substr($phone, 1);
        }

        if (str_starts_with($phone, '0')) {
            return '62'.substr($phone, 1);
        }

        return ltrim($phone, '+');
    }

    public function messageDelaySeconds(): int
    {
        return random_int(self::DELAY_MIN_SECONDS, self::DELAY_MAX_SECONDS);
    }

    private function token(): string
    {
        return (string) Setting::get('fonnte_token', '');
    }
}
