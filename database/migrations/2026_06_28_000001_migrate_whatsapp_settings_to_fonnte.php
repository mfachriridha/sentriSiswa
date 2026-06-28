<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const OLD_KEYS = [
        'whatsapp_cloud_access_token',
        'whatsapp_cloud_phone_number_id',
        'whatsapp_cloud_api_version',
        'whatsapp_cloud_base_url',
        'whatsapp_webhook_verify_token',
    ];

    public function up(): void
    {
        $legacyToken = DB::table('settings')
            ->where('key', 'whatsapp_cloud_access_token')
            ->value('value');

        if (is_string($legacyToken) && $legacyToken !== '') {
            DB::table('settings')->updateOrInsert(
                ['key' => 'fonnte_token'],
                ['value' => $legacyToken, 'created_at' => now(), 'updated_at' => now()],
            );
        }

        DB::table('settings')
            ->whereIn('key', self::OLD_KEYS)
            ->delete();

        foreach (self::OLD_KEYS as $key) {
            Cache::forget("setting:{$key}");
        }
        Cache::forget('setting:fonnte_token');
    }

    public function down(): void
    {
        $token = DB::table('settings')
            ->where('key', 'fonnte_token')
            ->value('value');

        if (is_string($token) && $token !== '') {
            DB::table('settings')->updateOrInsert(
                ['key' => 'whatsapp_cloud_access_token'],
                ['value' => $token, 'created_at' => now(), 'updated_at' => now()],
            );
        }

        DB::table('settings')
            ->where('key', 'fonnte_token')
            ->delete();

        Cache::forget('setting:whatsapp_cloud_access_token');
        Cache::forget('setting:fonnte_token');
    }
};
