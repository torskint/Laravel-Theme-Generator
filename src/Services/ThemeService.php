<?php

namespace Torskint\ThemeGenerator\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

class ThemeService
{
    public function generateTheme()
    {
        // Charger les couleurs du fichier manifest.json ou des valeurs par défaut
        $manifestPath = config('theme-generator.default_manifest');
        $colors = $this->getColorsFromManifest($manifestPath);

        // Si le dossier de ressources n'existe pas, le créer et copier les fichiers CSS par défaut
        $this->checkAndPrepareAssetDirectory();

        // Parcourir les fichiers CSS et appliquer les couleurs
        $cssDirectory = config('theme-generator.css_directory');
        $outputDirectory = config('theme-generator.output_directory');
        $this->applyColorsToCss($cssDirectory, $outputDirectory, $colors);
    }

    /**
     * Récupérer les couleurs du fichier manifest.json
     *
     * @param string $path
     * @return array
     */
    private function getColorsFromManifest(string $path)
    {
        if (!File::exists($path)) {
            return Config::get('theme-generator.default_colors');
        }

        return json_decode(File::get($path), true);
    }

    /**
     * Vérifier et préparer le dossier des assets
     */
    private function checkAndPrepareAssetDirectory()
    {
        $cssDirectory = config('theme-generator.css_directory');
        if (!File::exists($cssDirectory)) {
            // Si aucun fichier CSS n'existe, copier les fichiers par défaut dans le dossier
            $defaultAssets = resource_path('theme-generator/assets');
            File::copyDirectory($defaultAssets, $cssDirectory);
        }
    }

    /**
     * Appliquer les couleurs dans les fichiers CSS
     *
     * @param string $cssDirectory
     * @param string $outputDirectory
     * @param array $colors
     */
    private function applyColorsToCss($cssDirectory, $outputDirectory, $colors)
    {
        // Parcourir les fichiers CSS dans le dossier et remplacer les couleurs
        $files = File::allFiles($cssDirectory);
        foreach ($files as $file) {
            $content = File::get($file);
            foreach ($colors as $oldColor) {
                // Appliquer la logique de remplacement de couleur ici
                $newColor = $this->generateNewColor($oldColor); // Fonction à créer pour générer la nouvelle couleur
                $content = str_replace($oldColor, $newColor, $content);
            }
            $this->storeUpdatedCss($file, $content, $outputDirectory);
        }
    }

    /**
     * Appliquer la logique de génération de la nouvelle couleur en fonction des couleurs de base
     */
    private function generateNewColor($color)
    {
        // Ici, implémenter la logique pour générer une nouvelle couleur
        // en jouant sur la teinte, la saturation, etc.
        return $color;
    }

    /**
     * Sauvegarder le fichier CSS modifié
     */
    private function storeUpdatedCss($file, $content, $outputDirectory)
    {
        $outputPath = $outputDirectory . '/' . $file->getRelativePathname();
        File::put($outputPath, $content);
    }
}
