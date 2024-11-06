<?php

namespace Torskint\ThemeGenerator\Console\Commands;

use Illuminate\Console\Command;
use Torskint\ThemeGenerator\Helpers\ColorHelper;
use Illuminate\Support\Facades\File;

class GenerateThemeCommand extends Command
{
    protected $signature = 'theme:generate';
    protected $description = 'Generate a dynamic theme by modifying CSS colors and saving them in the public/assets directory.';

    protected $colorCorrespondance = [];

    /**
     * Exécuter la commande pour générer le thème.
     *
     * @return void
     */
    public function handle()
    {
        # Récupérer le chemin des ressources
        $publicAssetsPath = public_path('assets');
        $assetsPath = resource_path('theme-generator/assets');

        # Vérifier si le dossier 'assets' existe dans resources, sinon le créer et y copier les fichiers par défaut
        if (!File::exists($assetsPath)) {
            $this->info("Le dossier 'assets' n'existe pas, nous allons le créer et y copier les fichiers par défaut.");
            $this->copyDefaultAssets($publicAssetsPath, $assetsPath);
        }

        # Vérifier si le fichier manifest.json existe, sinon le créer avec des couleurs par défaut
        $manifestPath = resource_path('theme-generator/manifest.json');
        if (!File::exists($manifestPath)) {
            $this->info("Le fichier 'manifest.json' n'existe pas, il va être créé avec des couleurs par défaut.");
            $this->createDefaultManifest($manifestPath);
        }

        # Charger les couleurs du manifest
        $colors = json_decode(File::get($manifestPath), true);
        
        # Vérifier que les couleurs sont valides
        foreach ($colors as $color) {
            if (!ColorHelper::isValidHexColor($color)) {
                $this->error("La couleur '$color' dans le manifest est invalide.");
                return;
            }
        }

        # Modifier les couleurs dans le fichier CSS
        foreach ($colors as $color) {
            $palette = ColorHelper::generateHarmoniousColors($color);

            $this->colorCorrespondance[ $color ] = $palette[ array_rand($palette) ];
        }

        # Récupérer les fichiers CSS à modifier dans le dossier 'assets'
        $cssFiles = $this->getNeededFiles($assetsPath);

        foreach ($cssFiles as $file) {
            $currentFileDir = $file->getRelativePathname();

            # Lire le contenu du fichier CSS
            $cssContent = File::get($file);

            # Modifier les couleurs dans le fichier CSS
            foreach ($this->colorCorrespondance as $prevColor => $nextColor) {
                $cssContent = str_ireplace($prevColor, $nextColor, $cssContent);
            }

            # Créer le dossier correspondant dans public/assets si nécessaire
            $destinationPath = $publicAssetsPath . '/' . $currentFileDir;
            $destinationDir = dirname($destinationPath);
            if (!File::exists($destinationDir)) {
                File::makeDirectory($destinationDir, 0777, true, true);
            }

            # Sauvegarder le fichier CSS modifié dans public/assets
            File::put($destinationPath, $cssContent);
        }

        $this->info("Génération du thème terminée avec succès.");
    }


    protected function getNeededFiles($assetsPath)
    {
        # Récupérer tous les fichiers dans le répertoire
        $cssSvgJsFiles = File::allFiles($assetsPath);

        # Filtrer les fichiers par extension
        return collect($cssSvgJsFiles)->filter(function ($file) {
            return in_array($file->getExtension(), ['css', 'svg', 'js']);
        });
    }

    /**
     * Copier les fichiers CSS par défaut dans le dossier 'assets' si celui-ci n'existe pas.
     *
     * @param string $assetsPath
     * @return void
     */
    protected function copyDefaultAssets($publicAssetsPath, $assetsPath)
    {
        # Si le dossier des assets par défaut existe, on les copie
        if (File::exists($publicAssetsPath)) {
            File::copyDirectory($publicAssetsPath, $assetsPath);
            $this->info("Les fichiers par défaut ont été copiés dans le dossier 'assets'.");
        } else {
            $this->error("Le dossier des fichiers par défaut n'existe pas.");
        }
    }

    /**
     * Créer un fichier manifest.json avec des couleurs par défaut.
     *
     * @param string $manifestPath
     * @return void
     */
    protected function createDefaultManifest($manifestPath)
    {
        $defaultColors = [
            '#287F7A', # Exemple de couleur
            '#FF5722', # Exemple de couleur
            '#4CAF50', # Exemple de couleur
            '#FFFFFF', # Exemple de couleur (blanc)
        ];

        File::put($manifestPath, json_encode($defaultColors, JSON_PRETTY_PRINT));
        $this->info("Le fichier 'manifest.json' a été créé avec des couleurs par défaut.");
    }
}
