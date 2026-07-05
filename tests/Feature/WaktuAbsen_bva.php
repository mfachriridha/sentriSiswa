<?php

use App\Models\Pengaturan;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function waktuAbsenBvaAdmin(): Pengguna
{
    return Pengguna::factory()->admin()->create(['status' => 'registered']);
}

// ── Boundary: late tolerance vs attendance duration (start 06:00, end 07:00 → duration 60 minutes) ──

// TS.WKA.007 / TC.WKA.007.001 — tolerance exactly equal to the duration (at the upper bound, valid)
test('admin can save a late tolerance equal to the attendance duration', function () {
    $admin = waktuAbsenBvaAdmin();

    $this->actingAs($admin)->put(route('admin.pengaturan.waktu-absen.update'), [
        'attendance_start_hour' => '06',
        'attendance_start_minute' => '00',
        'attendance_end_hour' => '07',
        'attendance_end_minute' => '00',
        'attendance_late_tolerance_minutes' => '60',
    ])->assertRedirect(route('admin.pengaturan.waktu-absen.index'));

    expect(Pengaturan::get('attendance_late_tolerance_minutes'))->toBe('60');
});

// TS.WKA.008 / TC.WKA.008.001 — tolerance greater than the duration (just above the upper bound, invalid)
test('admin cannot save a late tolerance greater than the attendance duration', function () {
    $admin = waktuAbsenBvaAdmin();

    $this->actingAs($admin)->put(route('admin.pengaturan.waktu-absen.update'), [
        'attendance_start_hour' => '06',
        'attendance_start_minute' => '00',
        'attendance_end_hour' => '07',
        'attendance_end_minute' => '00',
        'attendance_late_tolerance_minutes' => '90',
    ])->assertSessionHasErrors('attendance_late_tolerance_minutes');
});
