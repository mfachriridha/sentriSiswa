<?php

return [
    'nama' => env('SEKOLAH_NAMA', 'SMA Negeri 1 ...'),

    'geofence' => [
        'type' => 'polygon',
        'coordinates' => [
            ['lat' => -6.1175404, 'lng' => 106.5770739],
            ['lat' => -6.1184805, 'lng' => 106.5773462],
            ['lat' => -6.1183004, 'lng' => 106.5782018],
            ['lat' => -6.1173483, 'lng' => 106.5779336],
            ['lat' => -6.1174344, 'lng' => 106.5775051],
        ],
    ],
];
