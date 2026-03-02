<?php

declare(strict_types=1);

namespace Bulbalara\CoreConfigMs\Support;

final class MethodsTransformer
{
    /**
     * @param  array<string, mixed>|null  $methods
     * @return array<string, mixed>|null
     */
    public static function normalize(?array $methods): ?array
    {
        if ($methods === null || $methods === []) {
            return null;
        }

        if (self::isAssoc($methods) && ! self::looksLikeUiMethods($methods)) {
            return $methods;
        }

        $normalized = [];

        foreach ($methods as $row) {
            if (! is_array($row)) {
                continue;
            }

            $method = $row['key'] ?? null;

            if (! is_string($method) || $method === '') {
                continue;
            }

            $arguments = $row['arguments'] ?? null;

            if (! is_array($arguments) || $arguments === []) {
                $normalized[$method] = null;

                continue;
            }

            $args = [];
            foreach ($arguments as $argumentRow) {
                if (! is_array($argumentRow)) {
                    continue;
                }

                $argKey = $argumentRow['key'] ?? null;

                if (! is_string($argKey) || $argKey === '') {
                    continue;
                }

                $args[$argKey] = $argumentRow['value'] ?? null;
            }

            $normalized[$method] = $args === [] ? null : $args;
        }

        return $normalized === [] ? null : $normalized;
    }

    /**
     * @param  array<string, mixed>|null  $methods
     * @return array<int, array<string, mixed>>
     */
    public static function denormalize(?array $methods): array
    {
        if ($methods === null || $methods === []) {
            return [];
        }

        if (! self::isAssoc($methods)) {
            return $methods;
        }

        $result = [];

        foreach ($methods as $method => $arguments) {
            if (! is_string($method) || $method === '') {
                continue;
            }

            $row = [
                'key' => $method,
                'arguments' => [],
            ];

            if (is_array($arguments)) {
                foreach ($arguments as $argKey => $argValue) {
                    if (! is_string($argKey) || $argKey === '') {
                        continue;
                    }

                    $row['arguments'][] = [
                        'key' => $argKey,
                        'value' => $argValue,
                    ];
                }
            } else {
                $row['arguments'] = null;
            }

            $result[] = $row;
        }

        return $result;
    }

    private static function isAssoc(array $value): bool
    {
        return array_keys($value) !== range(0, count($value) - 1);
    }

    private static function looksLikeUiMethods(array $methods): bool
    {
        foreach ($methods as $row) {
            if (is_array($row) && array_key_exists('key', $row)) {
                return true;
            }
        }

        return false;
    }
}
