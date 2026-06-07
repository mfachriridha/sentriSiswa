<?php

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can save wapisender configuration without sending a message', function () {
    $admin = User::factory()->admin()->create(['status' => 'registered']);

    $this->actingAs($admin)
        ->get(route('admin.settings.whatsapp.index'))
        ->assertSuccessful()
        ->assertSee('Wapisender');

    $this->actingAs($admin)
        ->put(route('admin.settings.whatsapp.update'), [
            'wapisender_api_key' => 'test-api-key',
            'wapisender_device_key' => 'WAPI-TEST',
            'wapisender_timeout_seconds' => 60,
            'wapisender_delay_min_seconds' => 8,
            'wapisender_delay_max_seconds' => 15,
        ])
        ->assertRedirect(route('admin.settings.whatsapp.index'));

    expect(Setting::get('wapisender_api_key'))->toBe('test-api-key');
    expect(Setting::get('wapisender_device_key'))->toBe('WAPI-TEST');
    expect(Setting::get('wapisender_timeout_seconds'))->toBe('60');
    expect(Setting::get('wapisender_delay_min_seconds'))->toBe('8');
    expect(Setting::get('wapisender_delay_max_seconds'))->toBe('15');
    expect(Setting::get('wapisender_is_priority'))->toBe('0');
    expect(Setting::get('wapisender_simulate_typing'))->toBe('0');
});

test('admin cannot save invalid wapisender delay range', function () {
    $admin = User::factory()->admin()->create(['status' => 'registered']);

    $this->actingAs($admin)
        ->from(route('admin.settings.whatsapp.index'))
        ->put(route('admin.settings.whatsapp.update'), [
            'wapisender_api_key' => 'test-api-key',
            'wapisender_device_key' => 'WAPI-TEST',
            'wapisender_timeout_seconds' => 60,
            'wapisender_delay_min_seconds' => 20,
            'wapisender_delay_max_seconds' => 10,
        ])
        ->assertRedirect(route('admin.settings.whatsapp.index'))
        ->assertSessionHasErrors('wapisender_delay_max_seconds');
});
