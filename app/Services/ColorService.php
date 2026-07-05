<?php

namespace App\Services;

class ColorService
{
    /**
     * Convert HEX to RGB string.
     */
    public static function hexToRgb(string $hex): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "rgb({$r}, {$g}, {$b})";
    }

    /**
     * Convert HEX to HSL string.
     */
    public static function hexToHsl(string $hex): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;

        if ($max === $min) {
            $h = $s = 0;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

            $h = match (true) {
                $max === $r => (($g - $b) / $d + ($g < $b ? 6 : 0)) / 6,
                $max === $g => (($b - $r) / $d + 2) / 6,
                default => (($r - $g) / $d + 4) / 6,
            };
        }

        $h = round($h * 360);
        $s = round($s * 100);
        $l = round($l * 100);

        return "hsl({$h}, {$s}%, {$l}%)";
    }

    /**
     * Convert HEX to CMYK string.
     */
    public static function hexToCmyk(string $hex): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        $k = 1 - max($r, $g, $b);
        if ($k >= 1) {
            return "cmyk(0%, 0%, 0%, 100%)";
        }

        $c = round((1 - $r - $k) / (1 - $k) * 100);
        $m = round((1 - $g - $k) / (1 - $k) * 100);
        $y = round((1 - $b - $k) / (1 - $k) * 100);
        $k = round($k * 100);

        return "cmyk({$c}%, {$m}%, {$y}%, {$k}%)";
    }

    /**
     * Auto-fill all color formats from HEX.
     */
    public static function fillFromHex(string $hex): array
    {
        return [
            'rgb_value' => self::hexToRgb($hex),
            'hsl_value' => self::hexToHsl($hex),
            'cmyk_value' => self::hexToCmyk($hex),
        ];
    }
}
