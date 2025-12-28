Datify for Laravel
==================

Datify auto-formats `created_at` and `updated_at` across your Laravel models. Set a default display for each column, opt into extra variants (humanized, ISO, calendar, etc.), and customize the attribute naming pattern—all via a single config file.

Why Datify?
-----------
- Consistent timestamp output without repeating `format()` everywhere.
- Drop-in trait: add to a base model to cover the whole project.
- Config-driven: toggle variants, set defaults per column, change attribute suffixes, or supply a custom PHP date format.

Requirements
------------
- PHP 8.1+
- Laravel 8-12 (illuminate/support, illuminate/database)

Installation
------------
```bash
composer require amrachraf6699/datify
php artisan vendor:publish --tag=datify-config
```

Quick Start
-----------
Add the trait to your base model (or any model you want to opt in):
```php
use Datify\Concerns\HasDatifyTimestamps;

class Model extends \Illuminate\Database\Eloquent\Model
{
    use HasDatifyTimestamps;
}
```

Configuration (`config/datify.php`)
-----------------------------------
Key options (see inline comments in the config for details):
- Boolean toggles: `to_date`, `to_time`, `to_date_time`, `to_day_date_time`, `to_iso_8601`, `to_iso`, `to_human`, `to_short_human`, `to_calendar`.
- `custom`: PHP date format string used when `custom` is selected; if `null`, raw DB timestamp is returned.
- `suffix`: naming pattern for appended attributes; `{format}` becomes the format key without `to_`. Default `_{format}` yields `created_at_human`, `updated_at_iso_8601`, etc.
- `defaults`: per-column default format for `created_at` and `updated_at` when you access them normally.

Example Config
--------------
```php
return [
    // Turn on extra accessors:
    'to_human' => true,
    'to_short_human' => true,
    'to_iso_8601' => true,
    'to_date_time' => true,

    // Custom naming (created_at.pretty, updated_at.pretty, etc.)
    'suffix' => '.{format}',

    // Custom format string (used when selecting 'custom')
    'custom' => 'd/m/Y H:i',

    // Default display for base attributes
    'defaults' => [
        'created_at' => 'to_human',
        'updated_at' => 'custom',
    ],
];
```

Attribute Outputs
-----------------
- Base attributes (`created_at`, `updated_at`): formatted using `defaults.created_at` / `defaults.updated_at`.
- Extra attributes: added only when the corresponding boolean toggle is `true`. Names follow `suffix` with `{format}` substituted (format key without `to_`).

Example Model Output
--------------------
Given `created_at = 2024-12-28 13:37:42` UTC, config with `suffix: '_{format}'`, `to_human: true`, `to_iso_8601: true`, `defaults.created_at: 'to_date_time'`:
```php
$post = Post::first();

$post->created_at;           // "2024-12-28 13:37:42"
$post->created_at_human;     // "2 hours ago"
$post->created_at_iso_8601;  // "2024-12-28T13:37:42+00:00"
```

Using a Custom Format Default
-----------------------------
```php
// config/datify.php
'custom' => 'd/m/Y h:i A',
'defaults' => [
    'created_at' => 'custom',
    'updated_at' => 'custom',
],

// Later
$user->created_at; // "28/12/2024 01:37 PM"
```

Raw Database Values
-------------------
- Use `$model->getRawOriginal('created_at')` or `->getRawOriginal('updated_at')` to bypass formatting.
- If `custom` is selected as the default and `custom` is `null`, Datify falls back to the raw DB timestamp string (e.g., `Y-m-d H:i:s`).

Integration Tips
----------------
- Add the trait to your base model to cover all models at once.
- Choose minimal toggles to avoid bloating serialized output; only enable the variants you need.
- When changing the `suffix`, remember it affects all appended attribute names; update API consumers accordingly.

Changelog & Contributing
------------------------
- Open a PR or issue at https://github.com/amrachraf6699/Datify once the repo is live.
- Please include Laravel/PHP versions and a reproduction snippet when filing bugs.
