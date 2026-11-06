<?php

return [
    'kredit_rate'   => (float) env('KREDIT_RATE', 0.065), // bunga flat per tahun
    'tenors'        => [12, 24, 36, 48, 60],
    'service_types' => ['Servis berkala', 'Ganti oli', 'Tune up', 'Rem & kaki-kaki', 'AC', 'Perbaikan umum'],
    'slots'         => ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'],
    'slot_capacity' => 3,
    'wa_url'        => env('WA_URL', 'https://api.fonnte.com/send'),
    'wa_token'      => env('WA_TOKEN'),
];
