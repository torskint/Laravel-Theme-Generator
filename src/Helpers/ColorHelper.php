<?php

namespace Torskint\ThemeGenerator\Helpers;

class ColorHelper
{
    /**
     * Vérifie si une couleur est valide au format hexadécimal.
     *
     * @param string $color
     * @return bool
     */
    public static function isValidHexColor($color)
    {
        return preg_match('/^#[a-fA-F0-9]{6}$/', $color) === 1;
    }

    /**
     * Convertir une couleur hexadécimale en RGB.
     *
     * @param string $hex
     * @return array
     */
    public static function hexToRgb($hex)
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) == 6) {
            list($r, $g, $b) = sscanf($hex, "%02x%02x%02x");
        } elseif (strlen($hex) == 3) {
            list($r, $g, $b) = sscanf($hex, "%1x%1x%1x");
            $r = $r * 17;
            $g = $g * 17;
            $b = $b * 17;
        } else {
            return [0, 0, 0]; // Couleur invalide
        }

        return [$r, $g, $b];
    }

    /**
     * Convertir une couleur RGB en Hex.
     *
     * @param int $r
     * @param int $g
     * @param int $b
     * @return string
     */
    public static function rgbToHex($r, $g, $b)
    {
        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }

    /**
     * Générer une couleur plus claire ou plus sombre.
     *
     * @param string $hex
     * @param float $factor
     * @return string
     */
    public static function adjustBrightness($hex, $factor)
    {
        list($r, $g, $b) = self::hexToRgb($hex);
        
        $r = max(0, min(255, (int)($r * $factor)));
        $g = max(0, min(255, (int)($g * $factor)));
        $b = max(0, min(255, (int)($b * $factor)));

        return self::rgbToHex($r, $g, $b);
    }

    /**
     * Générer une palette de couleurs pour une couleur donnée.
     *
     * @param string $color
     * @return array
     */
    public static function generateColorPalette($color)
    {
        $palette = [];
        
        // Définir les différentes variations de luminosité
        for ($i = -5; $i <= 5; $i++) {
            $factor = 1 + ($i * 0.1); // Déclencher des variations en 10% à chaque fois
            $palette[] = self::adjustBrightness($color, $factor);
        }

        return $palette;
    }

    public static function generateHarmoniousColors($hexColor, $numColors = 5) {
        // Convertir la couleur hexadécimale en HSL
        list($r, $g, $b) = sscanf($hexColor, "#%02x%02x%02x");
        $r /= 255;
        $g /= 255;
        $b /= 255;

        // Convertir RGB à HSL
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $delta = $max - $min;

        // Luminosité
        $l = ($max + $min) / 2;

        // Saturation
        if ($delta == 0) {
            $s = 0;
            $h = 0;
        } else {
            if ($max == $r) {
                $h = fmod((($g - $b) / $delta), 6);
            } elseif ($max == $g) {
                $h = (($b - $r) / $delta) + 2;
            } else {
                $h = (($r - $g) / $delta) + 4;
            }

            $s = $delta / (1 - abs(2 * $l - 1));
        }

        // Convertir HSL en valeurs entre 0 et 1
        $h /= 6;
        $h = fmod($h, 1);
        $s = min(max($s, 0), 1);
        $l = min(max($l, 0), 1);

        // Générer les couleurs similaires en ajustant la teinte et la luminosité
        $colors = [];
        for ($i = 0; $i < $numColors; $i++) {
            // Variation de la teinte : on ajoute une petite variation aléatoire
            $newH = fmod($h + (rand(-30, 30) / 100), 1); // variation de teinte (-30% à +30%)
            
            // Variation de la luminosité : on peut aussi ajuster la luminosité pour diversifier
            $newL = min(max($l + (rand(-10, 10) / 100), 0), 1); // variation de luminosité (-10% à +10%)

            // Convertir HSL en RGB
            $temp1 = $newL < 0.5 ? $newL * (1 + $s) : $newL + $s - $newL * $s;
            $temp2 = 2 * $newL - $temp1;

            $rgb = [];
            for ($j = 0; $j < 3; $j++) {
                $temp3 = $newH + (1 / 3) * $j;
                if ($temp3 < 0) $temp3 += 1;
                if ($temp3 > 1) $temp3 -= 1;
                if (6 * $temp3 < 1) {
                    $rgb[$j] = $temp2 + ($temp1 - $temp2) * 6 * $temp3;
                } elseif (2 * $temp3 < 1) {
                    $rgb[$j] = $temp1;
                } elseif (3 * $temp3 < 2) {
                    $rgb[$j] = $temp2 + ($temp1 - $temp2) * (0.666 - $temp3) * 6;
                } else {
                    $rgb[$j] = $temp2;
                }
            }

            // Convertir RGB en hex
            $r = round($rgb[0] * 255);
            $g = round($rgb[1] * 255);
            $b = round($rgb[2] * 255);
            $colors[] = sprintf("#%02x%02x%02x", $r, $g, $b);
        }

        return $colors;
    }
}
