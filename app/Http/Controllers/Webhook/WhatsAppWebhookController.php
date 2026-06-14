<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\WhatsappMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request): Response
    {
        $verifyToken = (string) Setting::get('whatsapp_webhook_verify_token', '');

        if (
            filled($verifyToken)
            && $request->query('hub_mode') === 'subscribe'
            && hash_equals($verifyToken, (string) $request->query('hub_verify_token'))
        ) {
            return response((string) $request->query('hub_challenge'), 200);
        }

        return response('Forbidden', 403);
    }

    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();

        foreach ($payload['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                foreach ($change['value']['statuses'] ?? [] as $statusPayload) {
                    $this->updateMessageStatus($statusPayload, $payload);
                }
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * @param  array<string, mixed>  $statusPayload
     * @param  array<string, mixed>  $payload
     */
    private function updateMessageStatus(array $statusPayload, array $payload): void
    {
        $providerMessageId = (string) ($statusPayload['id'] ?? '');

        if (blank($providerMessageId)) {
            return;
        }

        $whatsappMessage = WhatsappMessage::where('provider_message_id', $providerMessageId)->first();

        if (! $whatsappMessage) {
            return;
        }

        $status = (string) ($statusPayload['status'] ?? '');
        $response = [
            'webhook_status' => $statusPayload,
            'webhook_payload' => $payload,
        ];

        $updates = [
            'status' => $status ?: $whatsappMessage->status,
            'response' => json_encode($response),
        ];

        if (in_array($status, ['sent', 'delivered', 'read'], true) && ! $whatsappMessage->sent_at) {
            $updates['sent_at'] = now();
        }

        $whatsappMessage->update($updates);
    }
}
