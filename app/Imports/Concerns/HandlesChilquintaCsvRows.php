<?php

namespace App\Imports\Concerns;

use DateTimeImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use InvalidArgumentException;

trait HandlesChilquintaCsvRows
{
    /**
     * @param  array<string, mixed>  $row
     * @param  array<string, array<int, string>>  $aliases
     * @return array<string, mixed>
     */
    private function normalizeChilquintaRow(array $row, array $aliases): array
    {
        $normalized = [];

        foreach ($row as $heading => $value) {
            $key = $this->normalizeHeading((string) $heading);
            $normalized[$key] = is_string($value) ? trim($value) : $value;
        }

        $data = [];

        foreach ($aliases as $field => $headings) {
            $data[$field] = null;

            foreach ($headings as $heading) {
                $key = $this->normalizeHeading($heading);

                if (array_key_exists($key, $normalized)) {
                    $value = $normalized[$key];
                    $data[$field] = $value === '' ? null : $value;

                    break;
                }
            }
        }

        return $data;
    }

    private function normalizeHeading(string $heading): string
    {
        return Str::of($heading)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_')
            ->toString();
    }

    private function nullableInteger(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $clean = trim((string) $value);

        if (! preg_match('/^-?\d+$/', $clean)) {
            throw new InvalidArgumentException('Debe ser entero.');
        }

        return (int) $clean;
    }

    private function nullableDecimal(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $clean = str_replace(['$', ' '], '', trim((string) $value));

        if (str_contains($clean, ',') && ! str_contains($clean, '.')) {
            $clean = str_replace(',', '.', $clean);
        } else {
            $clean = str_replace(',', '', $clean);
        }

        if (! is_numeric($clean)) {
            throw new InvalidArgumentException('Debe ser numerico.');
        }

        return number_format((float) $clean, 2, '.', '');
    }

    private function parseFlexibleDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return Carbon::createFromTimestampUTC(((int) $value - 25569) * 86400)->toDateString();
        }

        $value = trim((string) $value);

        foreach ([
            'd/m/Y H:i:s',
            'd/m/Y H:i',
            'd/m/Y H',
            'd/m/Y',
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'Y-m-d H',
            'Y-m-d',
            'd-m-Y H:i:s',
            'd-m-Y H:i',
            'd-m-Y H',
            'd-m-Y',
        ] as $format) {
            $date = DateTimeImmutable::createFromFormat('!'.$format, $value);
            $errors = DateTimeImmutable::getLastErrors();

            if (
                $date instanceof DateTimeImmutable
                && $errors !== false
                && $errors['warning_count'] === 0
                && $errors['error_count'] === 0
                && $date->format($format) === $value
            ) {
                return $date->format('Y-m-d H:i:s');
            }
        }

        throw new InvalidArgumentException('Formato de fecha invalido.');
    }

    private function valueForReport(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return is_scalar($value) ? (string) $value : json_encode($value);
    }
}
