<?php

use App\Models\Pengaturan;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

function whatsappApiBvaAdmin(): Pengguna
{
    return Pengguna::factory()->admin()->create(['status' => 'registered']);
}

function whatsappApiBvaFakeSuccess(): void
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

// ── Boundary: fonnte_token length, max:255 ────────────────────────────────

// TS.WhatsappApi.005 / TC.WhatsappApi.005.001 — token with exactly 255 characters (at the maximum, valid)
test('admin can save a token with exactly 255 characters', function () {
    $token255 = str_repeat('a', 255);

    $this->actingAs(whatsappApiBvaAdmin())->put(route('admin.settings.whatsapp.update'), [
        'fonnte_token' => $token255,
    ])->assertRedirect(route('admin.settings.whatsapp.index'));

    expect(Pengaturan::get('fonnte_token'))->toBe($token255);
});

// TS.WhatsappApi.006 / TC.WhatsappApi.006.001 — token with 256 characters (just above the maximum, invalid)
test('admin cannot save a token with 256 characters', function () {
    $token256 = str_repeat('a', 256);

    $this->actingAs(whatsappApiBvaAdmin())->put(route('admin.settings.whatsapp.update'), [
        'fonnte_token' => $token256,
    ])->assertSessionHasErrors('fonnte_token');
});

// ── Boundary: test message phone length, max:20 ───────────────────────────

// TS.WhatsappApi.007 / TC.WhatsappApi.007.001 — phone with exactly 20 characters (at the maximum, valid)
test('admin can send a test message with a 20 character phone', function () {
    $admin = whatsappApiBvaAdmin();
    Pengaturan::set('fonnte_token', 'fonnte-test-token');
    whatsappApiBvaFakeSuccess();
    $phone20 = str_repeat('0', 20);

    $this->actingAs($admin)->postJson(route('admin.settings.whatsapp.test'), [
        'phone' => $phone20,
        'message' => 'Test batas nomor',
    ])->assertSuccessful();
});

// TS.WhatsappApi.008 / TC.WhatsappApi.008.001 — phone with 21 characters (just above the maximum, invalid)
test('admin cannot send a test message with a 21 character phone', function () {
    $admin = whatsappApiBvaAdmin();
    Pengaturan::set('fonnte_token', 'fonnte-test-token');
    $phone21 = str_repeat('0', 21);

    $this->actingAs($admin)->postJson(route('admin.settings.whatsapp.test'), [
        'phone' => $phone21,
        'message' => 'Test batas nomor',
    ])->assertStatus(422)
        ->assertJsonValidationErrors('phone');
});

// ── Boundary: test message length, max:500 ────────────────────────────────

// TS.WhatsappApi.009 / TC.WhatsappApi.009.001 — message with exactly 500 characters (at the maximum, valid)
test('admin can send a test message with exactly 500 characters', function () {
    $admin = whatsappApiBvaAdmin();
    Pengaturan::set('fonnte_token', 'fonnte-test-token');
    whatsappApiBvaFakeSuccess();
    $message500 = str_repeat('a', 500);

    $this->actingAs($admin)->postJson(route('admin.settings.whatsapp.test'), [
        'phone' => '08123456780',
        'message' => $message500,
    ])->assertSuccessful();
});

// TS.WhatsappApi.010 / TC.WhatsappApi.010.001 — message with 501 characters (just above the maximum, invalid)
test('admin cannot send a test message with 501 characters', function () {
    $admin = whatsappApiBvaAdmin();
    Pengaturan::set('fonnte_token', 'fonnte-test-token');
    $message501 = str_repeat('a', 501);

    $this->actingAs($admin)->postJson(route('admin.settings.whatsapp.test'), [
        'phone' => '08123456781',
        'message' => $message501,
    ])->assertStatus(422)
        ->assertJsonValidationErrors('message');
});
