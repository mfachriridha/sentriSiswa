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

// TS.WaktuAbsen.001 / TC.WaktuAbsen.001.001 — valid start/end/tolerance combination is saved (positive)
test('admin can save a valid attendance time configuration', function () {
    $admin = waktuAbsenAdmin();

    $this->actingAs($admin)->put(route('admin.settings.attendance-time.update'), waktuAbsenPayload())
        ->assertRedirect(route('admin.settings.attendance-time.index'));

    expect(Pengaturan::get('attendance_start_time'))->toBe('06:00');
    expect(Pengaturan::get('attendance_end_time'))->toBe('07:00');
    expect(Pengaturan::get('attendance_late_tolerance_minutes'))->toBe('30');
});

// TS.WaktuAbsen.002 / TC.WaktuAbsen.002.001 — end time equal to start time is rejected (negative)
test('admin cannot save attendance time when end equals start', function () {
    $admin = waktuAbsenAdmin();

    $this->actingAs($admin)->put(route('admin.settings.attendance-time.update'), waktuAbsenPayload([
        'attendance_end_hour' => '06',
        'attendance_end_minute' => '00',
    ]))->assertSessionHasErrors('attendance_end_hour');
});

// TS.WaktuAbsen.003 / TC.WaktuAbsen.003.001 — end time before start time is rejected (negative)
test('admin cannot save attendance time when end is before start', function () {
    $admin = waktuAbsenAdmin();

    $this->actingAs($admin)->put(route('admin.settings.attendance-time.update'), waktuAbsenPayload([
        'attendance_start_hour' => '07',
        'attendance_end_hour' => '06',
    ]))->assertSessionHasErrors('attendance_end_hour');
});

// TS.WaktuAbsen.004 / TC.WaktuAbsen.004.001 — late tolerance set to one of the allowed options (positive)
test('admin can save attendance time with a zero minute late tolerance', function () {
    $admin = waktuAbsenAdmin();

    $this->actingAs($admin)->put(route('admin.settings.attendance-time.update'), waktuAbsenPayload([
        'attendance_late_tolerance_minutes' => '0',
    ]))->assertRedirect(route('admin.settings.attendance-time.index'));

    expect(Pengaturan::get('attendance_late_tolerance_minutes'))->toBe('0');
});

// TS.WaktuAbsen.005 / TC.WaktuAbsen.005.001 — late tolerance not among the 10 fixed options is rejected (negative)
test('admin cannot save attendance time with a tolerance outside the fixed options', function () {
    $admin = waktuAbsenAdmin();

    $this->actingAs($admin)->put(route('admin.settings.attendance-time.update'), waktuAbsenPayload([
        'attendance_late_tolerance_minutes' => '25',
    ]))->assertSessionHasErrors('attendance_late_tolerance_minutes');
});

// TS.WaktuAbsen.006 / TC.WaktuAbsen.006.001 — start hour outside the registered 00-23 range is rejected (negative)
test('admin cannot save attendance time with an out of range hour', function () {
    $admin = waktuAbsenAdmin();

    $this->actingAs($admin)->put(route('admin.settings.attendance-time.update'), waktuAbsenPayload([
        'attendance_start_hour' => '24',
    ]))->assertSessionHasErrors('attendance_start_hour');
});
