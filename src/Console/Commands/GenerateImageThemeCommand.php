<?php

namespace Torskint\ThemeGenerator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManagerStatic as Image;

use Torskint\ThemeGenerator\Helpers\ColorHelper;
use Illuminate\Support\Str;

class GenerateImageThemeCommand extends Command
{
    protected $signature = 'theme:image {--theme=1}';
    protected $description = 'Change les couleurs dans les images PNG, JPG et SVG selon le manifest.json pour un thème spécifique.';

    public function handle()
    { 
        $themeOption    = $this->option('theme');
        $manifestPath   = resource_path('theme-generator/manifest.json');

        $themeOption            = $this->option('theme');
        $this->sourcePath       = resource_path('theme-generator/assets');
        $this->publicPath       = public_path('assets');
        $this->manifestPath     = resource_path('theme-generator/manifest.json');
        $this->themeFilePath    = resource_path('theme-generator/theme.json');
        $this->ImageCachePath   = resource_path('theme-generator/image-cache.json');
        $this->InitImageCache   = null;
        
        if (!File::exists($this->themeFilePath)) {
            return $this->error("Le fichier theme.json est introuvable.");
        }
        $themeData = json_decode(File::get($this->themeFilePath), true);

        $TN = "theme_{$themeOption}";
        if (!isset($themeData[$TN])) {
            return $this->error("Le thème $themeOption n'existe pas dans le theme.json.");
        }
        $colors = $themeData[$TN];

        $this->info("Modification des couleurs dans les images pour le thème $themeOption...");
        $this->processImages($this->sourcePath, $this->publicPath, $colors);

        $this->info("Les couleurs des images ont été modifiées avec succès !");
    }

    private function getFileInfo($file)
    {
        $extension      = $file->getExtension();
        $filePath       = $file->getPathname();

        $newFilePath = $this->publicPath . '/' . str_ireplace($this->sourcePath, '', $filePath);
        if ( !($file instanceof SymfonyFile) ) {
            $newFilePath = $this->publicPath . '/' . $file->getRelativePathname();
        }

        return compact('filePath', 'newFilePath');
    }

    protected function processImages(string $sourcePath, string $destinationPath, array $colors)
    {
        $this->InitImageCache = null;

        File::ensureDirectoryExists($destinationPath);

        if ( File::exists($this->ImageCachePath) ) {
            $this->info("Le cache existe déjà. Aucune nouvelle génération de cache.");
            return $this->processRasterImageUsingCache($colors);
        }

        $this->InitImageCache = [];
        foreach (File::allFiles($sourcePath) as $file) {
            if (in_array($extension, ['png', 'jpg', 'jpeg'])) {
                extract( $this->getFileInfo($file) );
                $this->processRasterImage($filePath, $newFilePath, $colors);
            }
        }

        if ( !empty($this->InitImageCache) ) {
            File::put($this->ImageCachePath, json_encode($this->InitImageCache, JSON_PRETTY_PRINT));
        }
    }

    protected function processRasterImageUsingCache(array $colors)
    {
        $fileCache = json_decode(File::get($this->ImageCachePath), true);

        foreach ($fileCache as $filePath) {
            $file = new SymfonyFile($filePath);

            // dd( $this->getFileInfo($file) );
            extract( $this->getFileInfo($file) );
            $this->processRasterImage($filePath, $newFilePath, $colors);
        }
    }

    private function couleursSimilaires(array $couleur1, array $couleur2, int $tolerance) {
        return abs($couleur1[0] - $couleur2['r']) <= $tolerance &&
               abs($couleur1[1] - $couleur2['g']) <= $tolerance &&
               abs($couleur1[2] - $couleur2['b']) <= $tolerance;
    }

    private function processRasterImage(string $filePath, string $newFilePath, array $colors, int $tolerance = 20)
    {
        $manager    = new ImageManager(['driver' => 'gd']);
        $image      = $manager->make($filePath);

        # Réduire temporairement la taille pour éviter la pixelisation
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        # Reduire l'image de manière proportionnelle pour éviter la pixelisation
        // $image->resize($originalWidth / 2, $originalHeight / 2);

        $couleurA   = ColorHelper::hexToRgb('#BB0B0B');
        $couleurB   = ColorHelper::hexToRgb('#6C0BBB');

        $largeur    = $image->width();
        $hauteur    = $image->height();

        $couleurAExist = false;
        for ($y = 0; $y < $hauteur; $y++) {
            for ($x = 0; $x < $largeur; $x++) {
                $couleurPixel = $image->pickColor($x, $y, 'array');

                # Vérifier si la couleur du pixel est similaire à la couleur A avec une tolérance
                if ($tolerance = $this->couleursSimilaires($couleurPixel, $couleurA, $tolerance)) {
                    $couleurAExist = true;
                    $image->pixel($couleurB, $x, $y);
                }
            }
        }

        # Appliquer un flou léger après avoir modifié l'image
        // $image->blur(1);

        # Remettre l'image à sa taille d'origine après les modifications
        // $image->resize($originalWidth, $originalHeight);

        # Sauvegarder l'image avec une qualité élevée
        $image->save($newFilePath, 90);

        if ( is_array($this->InitImageCache) AND $couleurAExist ) {
            $this->InitImageCache[] = $filePath;
        }

        $this->info("Image OK : $newFilePath");
    }
}
