<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function kelasBvaAdmin(): Pengguna
{
    return Pengguna::factory()->admin()->create(['status' => 'registered']);
}

// ── Boundary: identifier (nama field before composition) length, max:20 ───

// TS.KEL.008 / TC.KEL.008.001 — identifier with exactly 20 characters (at the maximum, valid)
test('admin can create a class with a 20 character identifier', function () {
    $identifier20 = str_repeat('A', 20);

    $this->actingAs(kelasBvaAdmin())->post(route('admin.kelas.store'), [
        'nama' => $identifier20,
        'tingkat' => '10',
    ])->assertRedirect(route('admin.kelas.index'));

    $this->assertDatabaseHas('kelas', [
        'nama' => '10 '.$identifier20,
        'tingkat' => '10',
    ]);
});

// TS.KEL.009 / TC.KEL.009.001 — identifier with 21 characters (just above the maximum, invalid)
test('admin cannot create a class with a 21 character identifier', function () {
    $identifier21 = str_repeat('A', 21);

    $this->actingAs(kelasBvaAdmin())->post(route('admin.kelas.store'), [
        'nama' => $identifier21,
        'tingkat' => '10',
    ])->assertSessionHasErrors('nama');
});
