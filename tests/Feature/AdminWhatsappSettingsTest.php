<?php

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('admin can save fonnte token without sending a message', function () {
    $admin = User::factory()->admin()->create(['status' => 'registered']);

    $this->actingAs($admin)
        ->get(route('admin.settings.whatsapp.index'))
        ->assertSuccessful()
        ->assertSee('WhatsApp API')
        ->assertSee('Token Fonnte')
        ->assertSee('Belum tersimpan');

    $this->actingAs($admin)
        ->put(route('admin.settings.whatsapp.update'), [
            'fonnte_token' => 'fonnte-test-token',
        ])
        ->assertRedirect(route('admin.settings.whatsapp.index'));

    expect(Setting::get('fonnte_token'))->toBe('fonnte-test-token');

    $this->actingAs($admin)
        ->get(route('admin.settings.whatsapp.index'))
        ->assertSuccessful()
        ->assertSee('********oken');
});

test('admin can clear stored fonnte token', function () {
    $admin = User::factory()->admin()->create(['status' => 'registered']);
    Setting::set('fonnte_token', 'fonnte-test-token');

    $this->actingAs($admin)
        ->put(route('admin.settings.whatsapp.update'), [
            'clear_fonnte_token' => '1',
        ])
        ->assertRedirect(route('admin.settings.whatsapp.index'));

    expect(Setting::get('fonnte_token'))->toBe('');
});

test('admin can send fonnte test message and repeated target is rate limited', function () {
    $admin = User::factory()->admin()->create(['status' => 'registered']);
    Setting::set('fonnte_token', 'fonnte-test-token');
    Http::fake([
        'api.fonnte.com/*' => Http::response([
            'detail' => 'success! message in queue',
            'id' => ['fonnte-message-id-1'],
            'process' => 'pending',
            'requestid' => 1,
            'status' => true,
            'target' => ['628123456789'],
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

        return $request->hasHeader('Authorization', 'fonnte-test-token')
            && $payload['target'] === '628123456789'
            && $payload['message'] === 'Test pesan'
            && $payload['countryCode'] === '62';
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

test('fonnte test returns error when provider connection fails', function () {
    $admin = User::factory()->admin()->create(['status' => 'registered']);
    Setting::set('fonnte_token', 'fonnte-test-token');
    Http::fake([
        'api.fonnte.com/*' => Http::failedConnection(),
    ]);

    $this->actingAs($admin)
        ->postJson(route('admin.settings.whatsapp.test'), [
            'phone' => '08123450000',
            'message' => 'Test pesan',
        ])
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('error', 'Koneksi ke Fonnte timeout atau tidak bisa dijangkau.');
});
