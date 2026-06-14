<?php

use App\Models\Setting;
use App\Models\User;
use App\Models\WhatsappMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('admin can save whatsapp cloud api configuration without sending a message', function () {
    $admin = User::factory()->admin()->create(['status' => 'registered']);

    $this->actingAs($admin)
        ->get(route('admin.settings.whatsapp.index'))
        ->assertSuccessful()
        ->assertSee('WhatsApp Cloud API')
        ->assertSee('Belum tersimpan');

    $this->actingAs($admin)
        ->put(route('admin.settings.whatsapp.update'), [
            'whatsapp_cloud_access_token' => 'test-access-token',
            'whatsapp_cloud_phone_number_id' => '1234567890',
            'whatsapp_cloud_business_account_id' => '9876543210',
            'whatsapp_cloud_api_version' => 'v23.0',
            'whatsapp_webhook_verify_token' => 'sentri-webhook-token',
        ])
        ->assertRedirect(route('admin.settings.whatsapp.index'));

    expect(Setting::get('whatsapp_cloud_access_token'))->toBe('test-access-token');
    expect(Setting::get('whatsapp_cloud_phone_number_id'))->toBe('1234567890');
    expect(Setting::get('whatsapp_cloud_business_account_id'))->toBe('9876543210');
    expect(Setting::get('whatsapp_cloud_api_version'))->toBe('v23.0');
    expect(Setting::get('whatsapp_webhook_verify_token'))->toBe('sentri-webhook-token');

    $this->actingAs($admin)
        ->get(route('admin.settings.whatsapp.index'))
        ->assertSuccessful()
        ->assertSee('********-token')
        ->assertSee('********7890');
});

test('admin can clear stored whatsapp cloud api credentials', function () {
    $admin = User::factory()->admin()->create(['status' => 'registered']);
    Setting::set('whatsapp_cloud_access_token', 'test-access-token');
    Setting::set('whatsapp_cloud_phone_number_id', '1234567890');
    Setting::set('whatsapp_cloud_business_account_id', '9876543210');
    Setting::set('whatsapp_webhook_verify_token', 'sentri-webhook-token');

    $this->actingAs($admin)
        ->put(route('admin.settings.whatsapp.update'), [
            'clear_whatsapp_cloud_access_token' => '1',
            'clear_whatsapp_cloud_phone_number_id' => '1',
            'clear_whatsapp_cloud_business_account_id' => '1',
            'clear_whatsapp_webhook_verify_token' => '1',
            'whatsapp_cloud_api_version' => 'v23.0',
        ])
        ->assertRedirect(route('admin.settings.whatsapp.index'));

    expect(Setting::get('whatsapp_cloud_access_token'))->toBe('');
    expect(Setting::get('whatsapp_cloud_phone_number_id'))->toBe('');
    expect(Setting::get('whatsapp_cloud_business_account_id'))->toBe('');
    expect(Setting::get('whatsapp_webhook_verify_token'))->toBe('');
});

test('admin can send whatsapp cloud api test message and repeated target is rate limited', function () {
    $admin = User::factory()->admin()->create(['status' => 'registered']);
    Setting::set('whatsapp_cloud_access_token', 'test-access-token');
    Setting::set('whatsapp_cloud_phone_number_id', '1234567890');
    Setting::set('whatsapp_cloud_api_version', 'v23.0');
    Http::fake([
        'https://graph.facebook.com/v23.0/1234567890/messages' => Http::response([
            'messaging_product' => 'whatsapp',
            'contacts' => [
                ['input' => '628123456789', 'wa_id' => '628123456789'],
            ],
            'messages' => [
                ['id' => 'wamid.test'],
            ],
        ]),
    ]);

    $this->actingAs($admin)
        ->postJson(route('admin.settings.whatsapp.test'), [
            'phone' => '08123456789',
            'message' => 'Test pesan',
        ])
        ->assertSuccessful()
        ->assertJson([
            'success' => true,
            'retry_after' => 60,
        ]);

    Http::assertSent(function ($request): bool {
        $payload = $request->data();

        return $request->hasHeader('Authorization', 'Bearer test-access-token')
            && $payload['messaging_product'] === 'whatsapp'
            && $payload['recipient_type'] === 'individual'
            && $payload['to'] === '628123456789'
            && $payload['type'] === 'text'
            && $payload['text']['body'] === 'Test pesan'
            && $payload['text']['preview_url'] === false;
    });

    $this->actingAs($admin)
        ->postJson(route('admin.settings.whatsapp.test'), [
            'phone' => '+628123456789',
            'message' => 'Test kedua',
        ])
        ->assertTooManyRequests()
        ->assertJsonPath('success', false);

    Http::assertSentCount(1);
});

test('whatsapp cloud api test returns json error when provider connection fails', function () {
    $admin = User::factory()->admin()->create(['status' => 'registered']);
    Setting::set('whatsapp_cloud_access_token', 'test-access-token');
    Setting::set('whatsapp_cloud_phone_number_id', '1234567890');
    Setting::set('whatsapp_cloud_api_version', 'v23.0');
    Http::fake([
        'https://graph.facebook.com/v23.0/1234567890/messages' => Http::failedConnection(),
    ]);

    $this->actingAs($admin)
        ->postJson(route('admin.settings.whatsapp.test'), [
            'phone' => '08123450000',
            'message' => 'Test pesan',
        ])
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('error', 'Koneksi ke WhatsApp Cloud API timeout atau tidak bisa dijangkau.');
});

test('meta can verify whatsapp webhook callback url with stored token', function () {
    Setting::set('whatsapp_webhook_verify_token', 'sentri-webhook-token');

    $this->get(route('whatsapp.webhook.verify', [
        'hub_mode' => 'subscribe',
        'hub_verify_token' => 'sentri-webhook-token',
        'hub_challenge' => 'challenge-from-meta',
    ]))
        ->assertSuccessful()
        ->assertContent('challenge-from-meta');
});

test('meta webhook verification rejects invalid token', function () {
    Setting::set('whatsapp_webhook_verify_token', 'sentri-webhook-token');

    $this->get(route('whatsapp.webhook.verify', [
        'hub_mode' => 'subscribe',
        'hub_verify_token' => 'wrong-token',
        'hub_challenge' => 'challenge-from-meta',
    ]))->assertForbidden();
});

test('meta webhook updates whatsapp message status from provider message id', function () {
    $whatsappMessage = WhatsappMessage::create([
        'recipient_phone' => '628123456789',
        'recipient_name' => 'Wali Kelas',
        'message_type' => 'attendance_report',
        'provider_message_id' => 'wamid.test',
        'message' => 'Laporan absensi',
        'status' => 'sent',
    ]);

    $this->postJson(route('whatsapp.webhook.handle'), [
        'object' => 'whatsapp_business_account',
        'entry' => [
            [
                'id' => 'waba-id',
                'changes' => [
                    [
                        'value' => [
                            'messaging_product' => 'whatsapp',
                            'statuses' => [
                                [
                                    'id' => 'wamid.test',
                                    'status' => 'delivered',
                                    'timestamp' => '1781428000',
                                    'recipient_id' => '628123456789',
                                ],
                            ],
                        ],
                        'field' => 'messages',
                    ],
                ],
            ],
        ],
    ])
        ->assertSuccessful()
        ->assertJson(['success' => true]);

    $whatsappMessage->refresh();

    expect($whatsappMessage->status)->toBe('delivered');
    expect($whatsappMessage->sent_at)->not->toBeNull();
    expect($whatsappMessage->response)->toContain('webhook_status');
});
