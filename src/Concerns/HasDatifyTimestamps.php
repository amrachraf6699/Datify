<?php

namespace Datify\Concerns;

use Carbon\CarbonInterface;
use DateTimeInterface;
use Datify\Support\DatifyFormatter;
use Illuminate\Support\Str;

trait HasDatifyTimestamps
{
    protected function initializeHasDatifyTimestamps(): void
    {
        $this->append($this->datifyAttributeNames());
    }

    public function getCreatedAtAttribute($value)
    {
        return $this->formatDatifyTimestamp($value, 'created_at');
    }

    public function getUpdatedAtAttribute($value)
    {
        return $this->formatDatifyTimestamp($value, 'updated_at');
    }

    public function getAttribute($key)
    {
        if ($parsed = $this->parseDatifyAttribute($key)) {
            [$column, $formatKey] = $parsed;

            return $this->formatExtra($column, $formatKey);
        }

        return parent::getAttribute($key);
    }

    public function __call($method, $parameters)
    {
        if (preg_match('/^get(.+)Attribute$/', $method, $matches)) {
            $attribute = Str::snake($matches[1]);

            if ($parsed = $this->parseDatifyAttribute($attribute)) {
                [$column, $formatKey] = $parsed;

                return $this->formatExtra($column, $formatKey);
            }
        }

        return parent::__call($method, $parameters);
    }

    protected function datifyAttributeNames(): array
    {
        $formats = $this->datifyFormatKeys();

        $appends = [];

        foreach ($formats as $formatKey) {
            if (config("datify.{$formatKey}", false)) {
                $appends[] = $this->datifyAttributeName('created_at', $formatKey);
                $appends[] = $this->datifyAttributeName('updated_at', $formatKey);
            }
        }

        return array_unique($appends);
    }

    protected function formatDatifyTimestamp($value, string $key): mixed
    {
        if ($value === null) {
            return null;
        }

        $date = $this->asDateTime($value);
        $raw = $this->datifyRaw($key);
        $format = config("datify.defaults.{$key}", config('datify.default', 'to_date_time'));

        return DatifyFormatter::format($date, $format, config('datify.custom'), $raw);
    }

    protected function formatExtra(string $key, string $formatKey): ?string
    {
        $date = $this->datifyDate($key);

        if (!$date) {
            return null;
        }

        return DatifyFormatter::format($date, $formatKey, config('datify.custom'), $this->datifyRaw($key));
    }

    protected function datifyDate(string $key): ?CarbonInterface
    {
        $original = $this->getRawOriginal($key);

        if ($original === null) {
            return null;
        }

        return $this->asDateTime($original);
    }

    protected function datifyRaw(string $key): mixed
    {
        return $this->getRawOriginal($key);
    }

    protected function datifyFormatKeys(): array
    {
        return [
            'to_date',
            'to_time',
            'to_date_time',
            'to_day_date_time',
            'to_iso_8601',
            'to_iso',
            'to_human',
            'to_short_human',
            'to_calendar',
        ];
    }

    protected function datifyAttributeName(string $column, string $formatKey): string
    {
        $suffix = config('datify.suffix', '_{format}');
        $formatSuffix = $this->datifyFormatSuffix($formatKey);

        return str_replace('{format}', $formatSuffix, $column . $suffix);
    }

    protected function datifyFormatSuffix(string $formatKey): string
    {
        return ltrim(preg_replace('/^to_/', '', $formatKey), '_');
    }

    protected function parseDatifyAttribute(string $attribute): ?array
    {
        $normalized = $this->normalizeDatifyAttribute($attribute);
        $columns = ['created_at', 'updated_at'];

        foreach ($columns as $column) {
            foreach ($this->datifyFormatKeys() as $formatKey) {
                $name = $this->datifyAttributeName($column, $formatKey);

                if (($attribute === $name || $normalized === $name) && config("datify.{$formatKey}", false)) {
                    return [$column, $formatKey];
                }
            }
        }

        return null;
    }

    protected function normalizeDatifyAttribute(string $attribute): string
    {
        // Insert underscores before numeric groups to match format suffixes like iso_8601.
        return preg_replace('/(?<=\\D)(\\d+)/', '_$1', $attribute);
    }
}
