<?php

use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('landing page renders hero background slideshow with three slides', function () {
    User::factory()->count(2)->student()->create()->each(function (User $student) {
        StudentProfile::factory()->create(['user_id' => $student->id]);
    });

    $response = $this->get(route('home'))->assertSuccessful();

    $response->assertSee('hero-slide');

    $response->assertSee('images/landing/lapangan-1.jpg', false);
    $response->assertSee('images/landing/lapangan-2.jpg', false);
    $response->assertSee('images/landing/kelas-11-ipa-6.jpg', false);

    $response->assertSee('animation-delay: 0s', false);
    $response->assertSee('animation-delay: 5s', false);
    $response->assertSee('animation-delay: 10s', false);
});

test('landing page does not render carousel card or broken navigation buttons', function () {
    $response = $this->get(route('home'))->assertSuccessful();

    $response->assertDontSee('class="carousel', false);
    $response->assertDontSee('class="carousel-item', false);
    $response->assertDontSee('sentriLandingSlide', false);
});

test('landing page hero has dark overlay for text readability', function () {
    $response = $this->get(route('home'))->assertSuccessful();

    $response->assertSee('bg-slate-950/55', false);
    $response->assertSee('aria-hidden="true"', false);
});

test('landing page still shows call-to-action and stats on top of hero background', function () {
    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee('Sentri Siswa untuk SMAN 11 Kabupaten Tangerang')
        ->assertSee('Masuk')
        ->assertSee('Daftar')
        ->assertSee('Siswa')
        ->assertSee('Guru')
        ->assertSee('Sistem kedisiplinan dan absensi sekolah');
});

test('landing page links the static hero stylesheet that drives the cross-fade', function () {
    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee('href="'.asset('css/landing-hero.css').'"', false);
});
