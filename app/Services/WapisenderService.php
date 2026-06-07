<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class WapisenderService
{
    private string $baseUrl;

    private string $apiKey;

    private string $deviceKey;

    private int $timeoutSeconds;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) Setting::get('wapisender_base_url', 'https://wapisender.id/api'), '/');
        $this->apiKey = (string) Setting::get('wapisender_api_key', '');
        $this->deviceKey = (string) Setting::get('wapisender_device_key', '');
        $this->timeoutSeconds = max(10, (int) Setting::get('wapisender_timeout_seconds', '60'));
    }

    public function isConfigured(): bool
    {
        return filled($this->apiKey) && filled($this->deviceKey);
    }

    /**
     * @param  array{is_priority?: bool, simulate_typing?: bool}  $options
     * @return array{success: bool, error?: string, response?: mixed}
     */
    public function send(string $target, string $message, array $options = []): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'API Key dan Device Key Wapisender belum dikonfigurasi.',
            ];
        }

        $request = Http::acceptJson()
            ->asJson()
            ->connectTimeout(10)
            ->timeout($this->timeoutSeconds)
            ->retry(2, 500);

        if (app()->environment('local')) {
            $request->withoutVerifying();
        }

        $response = $request->post($this->baseUrl.'/message/send', [
            'api_key' => $this->apiKey,
            'device_key' => $this->deviceKey,
            'to' => $this->normalizePhone($target),
            'message' => $message,
            'is_priority' => $options['is_priority'] ?? $this->defaultPriority(),
            'simulate_typing' => $options['simulate_typing'] ?? $this->defaultSimulateTyping(),
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
            'error' => $body['message'] ?? 'Gagal mengirim pesan WhatsApp.',
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

        return $phone;
    }

    public function messageDelaySeconds(): int
    {
        $min = max(1, (int) Setting::get('wapisender_delay_min_seconds', '8'));
        $max = max($min, (int) Setting::get('wapisender_delay_max_seconds', '15'));

        return random_int($min, $max);
    }

    private function defaultPriority(): bool
    {
        return filter_var(Setting::get('wapisender_is_priority', '0'), FILTER_VALIDATE_BOOLEAN);
    }

    private function defaultSimulateTyping(): bool
    {
        return filter_var(Setting::get('wapisender_simulate_typing', '0'), FILTER_VALIDATE_BOOLEAN);
    }
}
