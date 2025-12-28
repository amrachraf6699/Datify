<?php

namespace Datify\Support;

use Carbon\CarbonInterface;
use DateTimeInterface;

class DatifyFormatter
{
    public static function format(DateTimeInterface $date, string $format, ?string $custom = null, mixed $rawOriginal = null): string
    {
        return match ($format) {
            'to_date' => $date->format('Y-m-d'),
            'to_time' => $date->format('H:i:s'),
            'to_date_time' => $date->format('Y-m-d H:i:s'),
            'to_day_date_time' => $date->format('l, Y-m-d H:i:s'),
            'to_iso_8601' => $date->format(DATE_ATOM),
            'to_iso' => $date->format('c'),
            'to_human' => self::asCarbon($date)->diffForHumans(),
            'to_short_human' => self::asCarbon($date)->shortAbsoluteDiffForHumans(),
            'to_calendar' => self::asCarbon($date)->calendar(),
            'custom' => $custom !== null
                ? $date->format($custom)
                : (is_string($rawOriginal) ? $rawOriginal : $date->format('Y-m-d H:i:s')),
            default => $date->format('Y-m-d H:i:s'),
        };
    }

    protected static function asCarbon(DateTimeInterface $date): CarbonInterface
    {
        return $date instanceof CarbonInterface ? $date : \Carbon\Carbon::instance($date);
    }
}
