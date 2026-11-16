<?php

return [
    'enabled' => filter_var(env('GOOGLE_DRIVE_BACKUP_ENABLED', true), FILTER_VALIDATE_BOOL),

    'google_drive' => [
        'client_id' => env('GOOGLE_DRIVE_CLIENT_ID', ''),
        'client_secret' => env('GOOGLE_DRIVE_CLIENT_SECRET', ''),
        'refresh_token' => env('GOOGLE_DRIVE_REFRESH_TOKEN', ''),
        'folder_id' => env('GOOGLE_DRIVE_FOLDER_ID', ''),
    ],

    'retention_count' => max(1, (int) env('BACKUP_RETENTION_DAYS', 14)),

    'cron_token' => env('BACKUP_CRON_TOKEN', ''),

    'timezone' => env('APP_TIMEZONE', 'Asia/Dhaka'),

    'temp_directory' => storage_path('app/backups'),
];
