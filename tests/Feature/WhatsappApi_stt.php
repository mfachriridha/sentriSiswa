<?php

use App\Models\Pengaturan;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function whatsappApiSttAdmin(): Pengguna
{
    return Pengguna::factory()->admin()->create(['status' => 'registered']);
}

// State machine under test: TokenKosong ⇄ TokenTersimpan (fonnte_token empty vs filled)

// TS.WhatsappApi.011 / TC.WhatsappApi.011.001 — TokenKosong → simpan token → TokenTersimpan (positive)
test('whatsapp token transitions from kosong to tersimpan after saving', function () {
    $admin = whatsappApiSttAdmin();
    expect(Pengaturan::get('fonnte_token', ''))->toBe('');

    $this->actingAs($admin)->put(route('admin.settings.whatsapp.update'), [
        'fonnte_token' => 'token-pertama',
    ])->assertRedirect(route('admin.settings.whatsapp.index'));

    expect(Pengaturan::get('fonnte_token'))->toBe('token-pertama');
});

// TS.WhatsappApi.012 / TC.WhatsappApi.012.001 — TokenTersimpan → submit blank without checking clear → stays TokenTersimpan, old token kept (positive, no-op)
test('whatsapp token stays tersimpan when submitting blank without the clear checkbox', function () {
    $admin = whatsappApiSttAdmin();
    Pengaturan::set('fonnte_token', 'token-lama');

    $this->actingAs($admin)->put(route('admin.settings.whatsapp.update'), [
        'fonnte_token' => '',
    ])->assertRedirect(route('admin.settings.whatsapp.index'));

    expect(Pengaturan::get('fonnte_token'))->toBe('token-lama');
});

// TS.WhatsappApi.013 / TC.WhatsappApi.013.001 — TokenTersimpan → centang clear → TokenKosong (positive)
test('whatsapp token transitions from tersimpan to kosong when the clear checkbox is checked', function () {
    $admin = whatsappApiSttAdmin();
    Pengaturan::set('fonnte_token', 'token-lama');

    $this->actingAs($admin)->put(route('admin.settings.whatsapp.update'), [
        'clear_fonnte_token' => '1',
    ])->assertRedirect(route('admin.settings.whatsapp.index'));

    expect(Pengaturan::get('fonnte_token'))->toBe('');
});

// TS.WhatsappApi.014 / TC.WhatsappApi.014.001 — TokenKosong → submit blank (no token at all) → stays TokenKosong, explicit overwrite branch (positive)
test('whatsapp token stays kosong when submitting blank while nothing was ever saved', function () {
    $admin = whatsappApiSttAdmin();
    expect(Pengaturan::get('fonnte_token', ''))->toBe('');

    $this->actingAs($admin)->put(route('admin.settings.whatsapp.update'), [
        'fonnte_token' => '',
    ])->assertRedirect(route('admin.settings.whatsapp.index'));

    expect(Pengaturan::get('fonnte_token'))->toBe('');
});
