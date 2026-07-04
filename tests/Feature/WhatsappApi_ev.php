<?php

use App\Models\Pengaturan;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

function whatsappApiAdmin(): Pengguna
{
    return Pengguna::factory()->admin()->create(['status' => 'registered']);
}

function whatsappApiFakeSuccess(): void
{
    Http::fake([
        'api.fonnte.com/*' => Http::response([
            'detail' => 'success! message in queue',
            'id' => ['fonnte-message-id'],
            'process' => 'pending',
            'requestid' => 1,
            'status' => true,
            'target' => ['628123456789'],
        ]),
    ]);
}

// TS.WhatsappApi.001 / TC.WhatsappApi.001.001 — admin saves a new fonnte token (positive)
test('admin can save a new fonnte token', function () {
    $admin = whatsappApiAdmin();

    $this->actingAs($admin)->put(route('admin.settings.whatsapp.update'), [
        'fonnte_token' => 'token-baru-123',
    ])->assertRedirect(route('admin.settings.whatsapp.index'));

    expect(Pengaturan::get('fonnte_token'))->toBe('token-baru-123');
});

// TS.WhatsappApi.002 / TC.WhatsappApi.002.001 — sending a test message without any token configured fails (negative)
test('admin cannot send a test message when no token is configured', function () {
    $admin = whatsappApiAdmin();

    $this->actingAs($admin)->postJson(route('admin.settings.whatsapp.test'), [
        'phone' => '08123456789',
        'message' => 'Test tanpa token',
    ])->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('error', 'Token Fonnte belum dikonfigurasi. Isi token di pengaturan WhatsApp.');
});

// TS.WhatsappApi.003 / TC.WhatsappApi.003.001 — sending to the same number twice within 60 seconds is rate limited on the second attempt (negative)
test('sending a test message to the same number twice within 60 seconds is rate limited', function () {
    $admin = whatsappApiAdmin();
    Pengaturan::set('fonnte_token', 'fonnte-test-token');
    whatsappApiFakeSuccess();

    $this->actingAs($admin)->postJson(route('admin.settings.whatsapp.test'), [
        'phone' => '08199988877',
        'message' => 'Pesan pertama',
    ])->assertSuccessful();

    $this->actingAs($admin)->postJson(route('admin.settings.whatsapp.test'), [
        'phone' => '08199988877',
        'message' => 'Pesan kedua',
    ])->assertTooManyRequests()
        ->assertJsonPath('success', false);

    Http::assertSentCount(1);
});

// TS.WhatsappApi.004 / TC.WhatsappApi.004.001 — provider connection failure is reported as an error (negative)
test('test message reports an error when the provider connection fails', function () {
    $admin = whatsappApiAdmin();
    Pengaturan::set('fonnte_token', 'fonnte-test-token');
    Http::fake(['api.fonnte.com/*' => Http::failedConnection()]);

    $this->actingAs($admin)->postJson(route('admin.settings.whatsapp.test'), [
        'phone' => '08155566677',
        'message' => 'Test koneksi gagal',
    ])->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('error', 'Koneksi ke Fonnte timeout atau tidak bisa dijangkau.');
});
