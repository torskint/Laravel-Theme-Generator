<?php

namespace Torskint\ThemeGenerator\Helpers;

// namespace App\Helpers;

class ColorHelper
{

    public static function generateColorsFromBase($baseColor, $complementaryColor, $n)
    {
        $colors = [];
        $angleStep = 360 / $n;

        for ($i = 0; $i < $n; $i++) {
            $angle = $i * $angleStep;
            // Ajuste la couleur par rapport à l'angle pour créer une palette.
            $colors[] = self::adjustColor($baseColor, $angle);
        }

        return $colors;
    }
    /**
     * Génère une palette de couleurs harmonieuses basées sur un schéma donné.
     * 
     * @param int $n Le nombre de couleurs à générer.
     * @return array Liste des couleurs générées en format hexadécimal.
     * @throws \InvalidArgumentException
     */
    public static function generateHarmoniousColors($n)
    {
        // Validation du nombre de couleurs
        if ($n < 2 || $n > 10) {
            throw new \InvalidArgumentException("Le nombre de couleurs doit être compris entre 2 et 10.");
        }

        // Définir un schéma de couleurs de base
        $colorSchemes = [
            'complementary' => function($n) {
                // Générer une couleur de base aléatoire
                $baseColor = self::generateRandomColor();
                // Déterminer la couleur complémentaire (opposée)
                $complementaryColor = self::adjustColor($baseColor, 180); // Rotation de 180° pour obtenir la couleur complémentaire
                return self::generateColorsFromBase($baseColor, $complementaryColor, $n);
            },
            'analogous' => function($n) {
                // Générer une couleur de base aléatoire
                $baseColor = self::generateRandomColor();
                return self::generateAnalogousColors($baseColor, $n);
            },
            'triadic' => function($n) {
                // Générer une couleur de base aléatoire
                $baseColor = self::generateRandomColor();
                return self::generateTriadicColors($baseColor, $n);
            },
            'monochromatic' => function($n) {
                // Générer une couleur de base aléatoire
                $baseColor = self::generateRandomColor();
                return self::generateMonochromaticColors($baseColor, $n);
            }
        ];

        // Choisir un schéma de couleurs au hasard
        $selectedScheme = array_rand($colorSchemes);

        // Utiliser le schéma pour générer les couleurs
        return $colorSchemes[$selectedScheme]($n);
    }

    /**
     * Générer une couleur aléatoire au format hexadécimal.
     *
     * @return string
     */
    public static function generateRandomColor()
    {
        $r = rand(0, 255);
        $g = rand(0, 255);
        $b = rand(0, 255);
        return self::rgbToHex($r, $g, $b);
    }

    /**
     * Convertir une couleur RGB en hex.
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

    public static function hexToRgb(string $hex)
    {
        // Retirer le "#" si présent
        $hex = ltrim($hex, '#');

        // Convertir la couleur hexadécimale en valeurs RGB
        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Ajuster une couleur en fonction de l'angle de teinte (en degrés).
     *
     * @param string $hex
     * @param int $angle
     * @return string
     */
    public static function adjustColor($hex, $angle)
    {
        // Convertir la couleur hex en HSL
        $hsl = self::hexToHsl($hex);

        // Ajouter l'angle à la teinte (hue) pour obtenir une couleur différente
        $hsl['h'] = ($hsl['h'] + $angle) % 360;

        // Ajuster la luminosité pour s'assurer que la couleur est suffisamment foncée
        if ($hsl['l'] > 0.5) {
            $hsl['l'] = 0.4; // Réduire la luminosité si elle est trop claire
        }

        // Convertir de retour en hex
        return self::hslToHex($hsl['h'], $hsl['s'], $hsl['l']);
    }

    /**
     * Calculer la luminosité d'une couleur en HSL.
     *
     * @param string $hex
     * @return float
     */
    public static function getColorBrightness($hex)
    {
        $hsl = self::hexToHsl($hex);
        return $hsl['l']; // Retourne la luminosité (entre 0 et 1)
    }

    /**
     * Convertir Hex à HSL.
     *
     * @param string $hex
     * @return array
     */
    public static function hexToHsl($hex)
    {
        // Convertir Hex en RGB
        list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");

        // Normaliser les valeurs RGB
        $r /= 255;
        $g /= 255;
        $b /= 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);

        $h = $s = $l = ($max + $min) / 2;

        if ($max == $min) {
            $h = $s = 0; // achromatique
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
            switch ($max) {
                case $r:
                    $h = (($g - $b) / $d + ($g < $b ? 6 : 0)) * 60;
                    break;
                case $g:
                    $h = (($b - $r) / $d + 2) * 60;
                    break;
                case $b:
                    $h = (($r - $g) / $d + 4) * 60;
                    break;
            }
        }

        return ['h' => $h, 's' => $s, 'l' => $l];
    }

    /**
     * Convertir HSL en Hex.
     *
     * @param float $h
     * @param float $s
     * @param float $l
     * @return string
     */
    public static function hslToHex($h, $s, $l)
    {
        $h /= 360;
        $s = min(max($s, 0), 1);
        $l = min(max($l, 0), 1);

        $r = $g = $b = $l; // achromatique

        if ($s != 0) {
            $temp2 = $l < 0.5 ? $l * (1 + $s) : ($l + $s) - ($l * $s);
            $temp1 = 2 * $l - $temp2;

            $r = self::hueToRgb($temp1, $temp2, $h + 1 / 3);
            $g = self::hueToRgb($temp1, $temp2, $h);
            $b = self::hueToRgb($temp1, $temp2, $h - 1 / 3);
        }

        return self::rgbToHex(round($r * 255), round($g * 255), round($b * 255));
    }

    /**
     * Calculer la couleur correspondant à un angle sur le cercle chromatique.
     *
     * @param float $temp1
     * @param float $temp2
     * @param float $h
     * @return float
     */
    public static function hueToRgb($temp1, $temp2, $h)
    {
        if ($h < 0) $h += 1;
        if ($h > 1) $h -= 1;
        if ($h < 1 / 6) return $temp1 + ($temp2 - $temp1) * 6 * $h;
        if ($h < 1 / 2) return $temp2;
        if ($h < 2 / 3) return $temp1 + ($temp2 - $temp1) * (2 / 3 - $h) * 6;
        return $temp1;
    }

    // Autres fonctions pour générer des palettes analogues, triadiques, etc.

    public static function generateAnalogousColors($baseColor, $n)
    {
        $colors = [];
        $step = 360 / $n;
        for ($i = 0; $i < $n; $i++) {
            $colors[] = self::adjustColor($baseColor, $i * $step);
        }
        return $colors;
    }

    public static function generateTriadicColors($baseColor, $n)
    {
        $colors = [];
        $step = 360 / $n;
        for ($i = 0; $i < $n; $i++) {
            $colors[] = self::adjustColor($baseColor, $i * $step);
        }
        return $colors;
    }

    public static function generateMonochromaticColors($baseColor, $n)
    {
        $colors = [];
        for ($i = 0; $i < $n; $i++) {
            $colors[] = self::adjustColor($baseColor, $i * (360 / $n));
        }
        return $colors;
    }
}
