<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Throwable;

class WhatsAppCloudApiService
{
    public const TEST_COOLDOWN_SECONDS = 60;

    private const TIMEOUT_SECONDS = 60;

    private const CONNECT_TIMEOUT_SECONDS = 10;

    private const DELAY_MIN_SECONDS = 8;

    private const DELAY_MAX_SECONDS = 15;

    private string $baseUrl;

    private string $apiVersion;

    private string $accessToken;

    private string $phoneNumberId;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) Setting::get('whatsapp_cloud_base_url', 'https://graph.facebook.com'), '/');
        $this->apiVersion = trim((string) Setting::get('whatsapp_cloud_api_version', 'v23.0'), '/');
        $this->accessToken = (string) Setting::get('whatsapp_cloud_access_token', '');
        $this->phoneNumberId = (string) Setting::get('whatsapp_cloud_phone_number_id', '');
    }

    public function isConfigured(): bool
    {
        return filled($this->accessToken) && filled($this->phoneNumberId) && filled($this->apiVersion);
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
                'error' => 'Access Token dan Phone Number ID WhatsApp Cloud API belum dikonfigurasi.',
            ];
        }

        $timeout = max(5, (int) ($options['timeout'] ?? self::TIMEOUT_SECONDS));
        $connectTimeout = max(3, (int) ($options['connect_timeout'] ?? self::CONNECT_TIMEOUT_SECONDS));
        $retries = max(0, (int) ($options['retries'] ?? 1));

        $request = Http::acceptJson()
            ->asJson()
            ->withToken($this->accessToken)
            ->connectTimeout($connectTimeout)
            ->timeout($timeout)
            ->retry($retries, 500);

        if (app()->environment('local')) {
            $request->withoutVerifying();
        }

        try {
            $response = $request->post($this->messagesEndpoint(), [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $this->normalizePhone($target),
                'type' => 'text',
                'text' => [
                    'preview_url' => false,
                    'body' => $message,
                ],
            ]);
        } catch (ConnectionException) {
            return [
                'success' => false,
                'error' => 'Koneksi ke WhatsApp Cloud API timeout atau tidak bisa dijangkau.',
            ];
        } catch (Throwable) {
            return [
                'success' => false,
                'error' => 'Terjadi kesalahan saat menghubungi WhatsApp Cloud API.',
            ];
        }

        $body = $response->json();

        if (! is_array($body)) {
            return [
                'success' => false,
                'error' => 'WhatsApp Cloud API mengembalikan respons yang tidak valid.',
                'response' => $response->body(),
            ];
        }

        if ($response->successful() && isset($body['messages'][0]['id'])) {
            return [
                'success' => true,
                'response' => $body,
            ];
        }

        return [
            'success' => false,
            'error' => $body['error']['message'] ?? 'Gagal mengirim pesan WhatsApp.',
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

    private function messagesEndpoint(): string
    {
        return "{$this->baseUrl}/{$this->apiVersion}/{$this->phoneNumberId}/messages";
    }
}
