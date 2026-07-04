<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function kelasAdmin(): Pengguna
{
    return Pengguna::factory()->admin()->create(['status' => 'registered']);
}

// TS.Kelas.001 / TC.Kelas.001.001 — create class with a wali kelas assigned (positive)
test('admin can create a class with a homeroom teacher', function () {
    $admin = kelasAdmin();
    $wali = Pengguna::factory()->homeroom()->create();

    $this->actingAs($admin)->post(route('admin.kelas.store'), [
        'nama' => '1',
        'tingkat' => '10',
        'wali_kelas_id' => (string) $wali->id,
    ])->assertRedirect(route('admin.kelas.index'));

    $this->assertDatabaseHas('kelas', [
        'nama' => '10. 1',
        'tingkat' => '10',
        'wali_kelas_id' => (string) $wali->id,
    ]);
});

// TS.Kelas.002 / TC.Kelas.002.001 — create class with a duplicate nama+tingkat combination (negative)
test('admin cannot create a class with a duplicate nama and tingkat combination', function () {
    $admin = kelasAdmin();
    Kelas::create(['nama' => '10. 2', 'tingkat' => '10']);

    $this->actingAs($admin)->post(route('admin.kelas.store'), [
        'nama' => '2',
        'tingkat' => '10',
    ])->assertSessionHasErrors('nama');

    expect(Kelas::where('nama', '10. 2')->where('tingkat', '10')->count())->toBe(1);
});

// TS.Kelas.003 / TC.Kelas.003.001 — create class with an invalid tingkat (negative)
test('admin cannot create a class with an invalid tingkat', function () {
    $admin = kelasAdmin();

    $this->actingAs($admin)->post(route('admin.kelas.store'), [
        'nama' => '3',
        'tingkat' => '13',
    ])->assertSessionHasErrors('tingkat');
});

// TS.Kelas.004 / TC.Kelas.004.001 — create class with a wali_kelas_id that does not exist (negative)
test('admin cannot create a class with a non-existent wali kelas', function () {
    $admin = kelasAdmin();

    $this->actingAs($admin)->post(route('admin.kelas.store'), [
        'nama' => '4',
        'tingkat' => '10',
        'wali_kelas_id' => 'guru-tidak-ada',
    ])->assertSessionHasErrors('wali_kelas_id');
});

// TS.Kelas.005 / TC.Kelas.005.001 — create class without a wali kelas, saved as null (positive, nullable)
test('admin can create a class without a homeroom teacher', function () {
    $admin = kelasAdmin();

    $this->actingAs($admin)->post(route('admin.kelas.store'), [
        'nama' => '5',
        'tingkat' => '10',
    ])->assertRedirect(route('admin.kelas.index'));

    $this->assertDatabaseHas('kelas', [
        'nama' => '10. 5',
        'tingkat' => '10',
        'wali_kelas_id' => null,
    ]);
});

// TS.Kelas.006 / TC.Kelas.006.001 — update class succeeds (positive)
test('admin can update a class', function () {
    $admin = kelasAdmin();
    $class = Kelas::create(['nama' => '10. 6', 'tingkat' => '10']);
    $wali = Pengguna::factory()->homeroom()->create();

    $this->actingAs($admin)->put(route('admin.kelas.update', $class), [
        'nama' => '6B',
        'tingkat' => '11',
        'wali_kelas_id' => (string) $wali->id,
    ])->assertRedirect(route('admin.kelas.index'));

    expect($class->fresh()->nama)->toBe('11 6B');
    expect($class->fresh()->tingkat)->toBe('11');
    expect($class->fresh()->wali_kelas_id)->toEqual($wali->id);
});

// TS.Kelas.007 / TC.Kelas.007.001 — delete class succeeds (positive)
test('admin can delete a class', function () {
    $admin = kelasAdmin();
    $class = Kelas::create(['nama' => '10. 7', 'tingkat' => '10']);

    $this->actingAs($admin)->delete(route('admin.kelas.destroy', $class))
        ->assertRedirect(route('admin.kelas.index'));

    $this->assertDatabaseMissing('kelas', ['id' => $class->id]);
});
