<?php

namespace Torskint\ThemeGenerator\Console\Commands;

use Illuminate\Console\Command;
use Torskint\ThemeGenerator\Helpers\ColorHelper;
use Illuminate\Support\Facades\File;

class GenerateThemeCommand extends Command
{
    protected $signature      = 'theme:generate {--theme=}';
    protected $description    = 'Génère des thèmes pour l\'application.';

    public function handle()
    {
        $themeOption            = $this->option('theme');

        $this->sourcePath       = config('css_directory');
        $this->publicPath       = config('output_directory');
        $this->manifestPath     = config('default_manifest');
        $this->themeFilePath    = config('theme_file_path');

        if (!File::exists($this->sourcePath)) {
            $this->info("Copie initiale des fichiers depuis public/assets vers resources/theme-generator/assets.");
            File::copyDirectory($this->publicPath, $this->sourcePath);
        }

        if (!File::exists($this->manifestPath)) {
            File::put($this->manifestPath, json_encode([], JSON_PRETTY_PRINT));
            $this->info("Fichier manifest.json créé avec les couleurs par défaut.");
        }

        if (!File::exists($this->themeFilePath)) {
            File::put($this->themeFilePath, json_encode([
                'theme_1' => []
            ], JSON_PRETTY_PRINT));
            $this->info("Fichier theme.json créé avec succès.");
        }

        $manifest = json_decode(File::get($this->manifestPath), true);
        if ( empty($themeOption) ) {
            $this->info("Génération de 100 thèmes aléatoires...");
            $this->generate100Themes($manifest, $this->sourcePath, $this->publicPath);
        } else {
            $this->info("Application du thème: theme_$themeOption");
            $this->applyTheme($manifest, $themeOption, $this->sourcePath, $this->publicPath);
        }

        $this->info("Thèmes générés avec succès !");
    }

    protected function generate100Themes(array $manifest_colors, string $sourcePath, string $publicPath)
    {
        $manifestJsonContent = [];
        for ($i = 1; $i <= 100; $i++) {
            $manifestJsonContent[ "theme_{$i}" ] = $this->generateNewColors($manifest_colors);
        }

        File::put($this->themeFilePath, json_encode($manifestJsonContent, JSON_PRETTY_PRINT));
    }

    protected function applyTheme(array $manifest, string $themeOption, string $sourcePath, string $publicPath)
    {
        $theme = json_decode(File::get($this->themeFilePath), true);

        $themeName = "theme_{$themeOption}";
        if ( !isset($theme[$themeName]) || empty($theme[$themeName]) ) {
            return $this->error("Thème $themeOption non trouvé dans le theme.json ou vide.");
        }
        $colors = $theme[$themeName];

        $this->applyColorsToFiles($colors, $this->sourcePath, $this->publicPath);
    }

    protected function generateNewColors(array $originalColors)
    {
        $newColors = [];
        
        foreach ($originalColors as $colorHex) {
            $pluriColors = array_map('trim', explode('|', $colorHex));

            $subHex = $this->generateVariantColor( $pluriColors[0] );
            foreach ($pluriColors as $color) {
                $newColors[$color] = $subHex;
            }
        }

        return $newColors;
    }

    protected function generateVariantColor($colorHex)
    {
        // Convert hex color to RGB
        list($r, $g, $b) = sscanf($colorHex, "#%02x%02x%02x");

        // Convert RGB to HSV
        $hsv = $this->rgbToHsv($r, $g, $b);

        // Adjust the hue by a random offset to create variation
        $hsv['h'] = fmod(($hsv['h'] + rand(20, 340)), 360); // Shift hue within 20-340 degrees for variety

        // Convert HSV back to RGB
        list($newR, $newG, $newB) = $this->hsvToRgb($hsv['h'], $hsv['s'], $hsv['v']);

        // Convert RGB back to hex
        return sprintf("#%02x%02x%02x", $newR, $newG, $newB);
    }

    protected function rgbToHsv($r, $g, $b)
    {
        $r /= 255;
        $g /= 255;
        $b /= 255;
        
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $delta = $max - $min;

        $h = 0;
        $s = $max == 0 ? 0 : ($delta / $max);
        $v = $max;

        if ($delta != 0) {
            if ($max === $r) {
                $h = 60 * (($g - $b) / $delta % 6);
            } elseif ($max === $g) {
                $h = 60 * (($b - $r) / $delta + 2);
            } else {
                $h = 60 * (($r - $g) / $delta + 4);
            }
        }

        if ($h < 0) $h += 360;

        return ['h' => $h, 's' => $s, 'v' => $v];
    }

    protected function hsvToRgb($h, $s, $v)
    {
        $c = $v * $s;
        $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
        $m = $v - $c;

        if ($h < 60) {
            $r = $c; $g = $x; $b = 0;
        } elseif ($h < 120) {
            $r = $x; $g = $c; $b = 0;
        } elseif ($h < 180) {
            $r = 0; $g = $c; $b = $x;
        } elseif ($h < 240) {
            $r = 0; $g = $x; $b = $c;
        } elseif ($h < 300) {
            $r = $x; $g = 0; $b = $c;
        } else {
            $r = $c; $g = 0; $b = $x;
        }

        $r = ($r + $m) * 255;
        $g = ($g + $m) * 255;
        $b = ($b + $m) * 255;

        return [round($r), round($g), round($b)];
    }

    protected function applyColorsToFiles(array $colors, string $sourcePath, string $destinationPath)
    {
        foreach (File::allFiles($sourcePath) as $file) {

            # Créer le dossier correspondant dans public/assets si nécessaire
            $destinationPath    = $this->publicPath . '/' . $file->getRelativePathname();
            $destinationDir     = dirname($destinationPath);
            if (!File::exists($destinationDir)) {
                File::makeDirectory($destinationDir, 0777, true, true);
            }

            $content = File::get($file->getPathname());
            foreach ($colors as $originalColor => $newColor) {
                $content = str_ireplace($originalColor, $newColor, $content);
            }
            File::put($destinationPath, $content);

        }
    }
}
