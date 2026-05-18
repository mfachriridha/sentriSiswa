<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create([
        'email' => 'admin@sekolah.sch.id',
        'password' => 'admin123',
        'role' => 'admin',
        'name' => 'Admin',
    ]);
});

test('landing page returns 200 with branding', function () {
    $this->get('/')->assertOk()->assertSee('Sentri Siswa');
});

test('login page returns 200 with branding', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('Sentri Siswa')
        ->assertSee('Masuk');
});

test('register page returns 200 with branding', function () {
    $this->get('/register')
        ->assertOk()
        ->assertSee('Sentri Siswa');
});

test('admin can login and redirect to dashboard', function () {
    $response = $this->post('/login', [
        'email' => $this->admin->email,
        'password' => 'admin123',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticated();
});

test('admin dashboard loads without error', function () {
    $this->actingAs($this->admin)
        ->get('/admin/dashboard')
        ->assertOk()
        ->assertDontSee('Internal Server Error')
        ->assertDontSee('Route [');
});

test('all admin routes load without error', function (string $route) {
    $this->actingAs($this->admin)
        ->get($route)
        ->assertOk()
        ->assertDontSee('Internal Server Error')
        ->assertDontSee('Route [');
})->with([
    '/admin/kelas',
    '/admin/siswa',
    '/admin/guru',
    '/admin/import',
    '/admin/poin-pelanggaran',
    '/admin/catat-pelanggaran',
    '/admin/pengajuan-poin',
    '/admin/tata-tertib',
    '/admin/absensi',
    '/admin/konfigurasi',
]);

test('guest cannot access admin routes', function () {
    $this->get('/admin/dashboard')
        ->assertRedirect('/login');
});
