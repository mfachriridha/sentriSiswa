<?php

use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests can see the landing page from the root page', function () {
    User::factory()->count(2)->homeroom()->create();
    User::factory()
        ->count(3)
        ->student()
        ->create()
        ->each(fn (User $student) => StudentProfile::factory()->create([
            'user_id' => $student->id,
        ]));

    $this->get('/')
        ->assertSuccessful()
        ->assertSee('SMAN 11 Kabupaten Tangerang')
        ->assertSee('Masuk')
        ->assertSee('Daftar')
        ->assertSee('Siswa')
        ->assertSee('Guru')
        ->assertSee('Jl. K.H. Hasyim Ashari')
        ->assertSee('Kebijakan Privasi')
        ->assertSee('Ketentuan Layanan');
});

test('authenticated users are redirected from root page to their dashboard', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/')
        ->assertRedirect(route('admin.dashboard'));
});

test('guests can see public legal pages', function () {
    $this->get(route('privacy-policy'))
        ->assertSuccessful()
        ->assertSee('Kebijakan Privasi')
        ->assertSee('Notifikasi WhatsApp');

    $this->get(route('terms-of-service'))
        ->assertSuccessful()
        ->assertSee('Ketentuan Layanan')
        ->assertSee('Notifikasi WhatsApp');
});
