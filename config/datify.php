<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Toggle Optional Timestamp Variants
    |--------------------------------------------------------------------------
    | Set any of these to true to append the matching accessors
    | (e.g., created_at_human, updated_at_iso_8601).
    */
    'to_date' => false,          // 2024-12-28
    'to_time' => false,          // 13:37:42
    'to_date_time' => false,     // 2024-12-28 13:37:42
    'to_day_date_time' => false, // Saturday, 2024-12-28 13:37:42
    'to_iso_8601' => false,      // 2024-12-28T13:37:42+00:00
    'to_iso' => false,           // Alias to Carbon 'c' format
    'to_human' => false,         // "2 hours ago"
    'to_short_human' => false,   // "2h"
    'to_calendar' => false,      // "Yesterday at 1:37 PM"

    /*
    |--------------------------------------------------------------------------
    | Custom Format
    |--------------------------------------------------------------------------
    | Provide a PHP date format string (e.g., 'd/m/Y H:i'). If you set
    | defaults.* to 'custom' and this is null, the raw DB timestamp is returned.
    */
    'custom' => null,

    /*
    |--------------------------------------------------------------------------
    | Attribute Suffix Pattern
    |--------------------------------------------------------------------------
    | Controls how appended attributes are named. `{format}` is replaced with
    | the format key minus the leading "to_". Example: '_{format}' -> created_at_human.
    */
    'suffix' => '_{format}',

    /*
    |--------------------------------------------------------------------------
    | Default Formats
    |--------------------------------------------------------------------------
    | Primary format used when accessing created_at / updated_at normally.
    | Accepts any key above or 'custom'.
    */
    'defaults' => [
        'created_at' => 'to_date_time',
        'updated_at' => 'to_date_time',
    ],
];
