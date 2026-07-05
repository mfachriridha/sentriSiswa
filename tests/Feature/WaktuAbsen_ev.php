<?php

use App\Models\Pengaturan;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function waktuAbsenAdmin(): Pengguna
{
    return Pengguna::factory()->admin()->create(['status' => 'registered']);
}

function waktuAbsenPayload(array $overrides = []): array
{
    return array_merge([
        'attendance_start_hour' => '06',
        'attendance_start_minute' => '00',
        'attendance_end_hour' => '07',
        'attendance_end_minute' => '00',
        'attendance_late_tolerance_minutes' => '30',
    ], $overrides);
}

// TS.WKA.001 / TC.WKA.001.001 — valid start/end/tolerance combination is saved (positive)
test('admin can save a valid attendance time configuration', function () {
    $admin = waktuAbsenAdmin();

    $this->actingAs($admin)->put(route('admin.pengaturan.waktu-absen.update'), waktuAbsenPayload())
        ->assertRedirect(route('admin.pengaturan.waktu-absen.index'));

    expect(Pengaturan::get('attendance_start_time'))->toBe('06:00');
    expect(Pengaturan::get('attendance_end_time'))->toBe('07:00');
    expect(Pengaturan::get('attendance_late_tolerance_minutes'))->toBe('30');
});

// TS.WKA.002 / TC.WKA.002.001 — end time equal to start time is rejected (negative)
test('admin cannot save attendance time when end equals start', function () {
    $admin = waktuAbsenAdmin();

    $this->actingAs($admin)->put(route('admin.pengaturan.waktu-absen.update'), waktuAbsenPayload([
        'attendance_end_hour' => '06',
        'attendance_end_minute' => '00',
    ]))->assertSessionHasErrors('attendance_end_hour');
});

// TS.WKA.003 / TC.WKA.003.001 — end time before start time is rejected (negative)
test('admin cannot save attendance time when end is before start', function () {
    $admin = waktuAbsenAdmin();

    $this->actingAs($admin)->put(route('admin.pengaturan.waktu-absen.update'), waktuAbsenPayload([
        'attendance_start_hour' => '07',
        'attendance_end_hour' => '06',
    ]))->assertSessionHasErrors('attendance_end_hour');
});

// TS.WKA.004 / TC.WKA.004.001 — late tolerance set to one of the allowed options (positive)
test('admin can save attendance time with a zero minute late tolerance', function () {
    $admin = waktuAbsenAdmin();

    $this->actingAs($admin)->put(route('admin.pengaturan.waktu-absen.update'), waktuAbsenPayload([
        'attendance_late_tolerance_minutes' => '0',
    ]))->assertRedirect(route('admin.pengaturan.waktu-absen.index'));

    expect(Pengaturan::get('attendance_late_tolerance_minutes'))->toBe('0');
});

// TS.WKA.005 / TC.WKA.005.001 — late tolerance not among the 10 fixed options is rejected (negative)
test('admin cannot save attendance time with a tolerance outside the fixed options', function () {
    $admin = waktuAbsenAdmin();

    $this->actingAs($admin)->put(route('admin.pengaturan.waktu-absen.update'), waktuAbsenPayload([
        'attendance_late_tolerance_minutes' => '25',
    ]))->assertSessionHasErrors('attendance_late_tolerance_minutes');
});

// TS.WKA.006 / TC.WKA.006.001 — start hour outside the registered 00-23 range is rejected (negative)
test('admin cannot save attendance time with an out of range hour', function () {
    $admin = waktuAbsenAdmin();

    $this->actingAs($admin)->put(route('admin.pengaturan.waktu-absen.update'), waktuAbsenPayload([
        'attendance_start_hour' => '24',
    ]))->assertSessionHasErrors('attendance_start_hour');
});
