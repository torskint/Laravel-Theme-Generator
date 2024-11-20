<?php

namespace Torskint\ThemeGenerator;

use Illuminate\Support\ServiceProvider;
use Torskint\ThemeGenerator\Console\Commands\GenerateThemeCommand;
use Torskint\ThemeGenerator\Console\Commands\GenerateImageThemeCommand;
use Intervention\Image\ImageManagerStatic as Image;
use Intervention\Image\ImageManager;

use Torskint\ThemeGenerator\Http\Middleware\SetThemeMiddleware;

class ThemeGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Enregistrer la commande Artisan
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateThemeCommand::class,
                GenerateImageThemeCommand::class,
            ]);
        }

        // Charger les helpers
        $this->loadHelpers();

        // Publier le fichier de configuration dans le dossier config/
        $this->publishes([
            __DIR__.'/../../config/theme-generator.php' => config_path('theme-generator.php'),
        ], 'config');

        // Publier les fichiers CSS générés dans le dossier public
        $this->publishes([
            __DIR__ . '/../public/assets' => public_path('assets/theme-generator'),
        ], 'public');

        // Enregistrement du middleware pour gérer le thème
        $this->app['router']->pushMiddlewareToGroup('web', SetThemeMiddleware::class);

        // Charger les vues si nécessaires
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'theme-generator');
    }


    /**
     * Charger les helpers du package.
     *
     * @return void
     */
    protected function loadHelpers()
    {
        foreach (glob(__DIR__.'/Helpers/*.php') as $file) {
            require_once $file;
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // dd(new ImageManager);
        // Enregistrement d'une instance globale pour Image (Intervention Image)
        // $this->app->singleton('Image', function ($app) {
        //     return new Image();
        // });
        
        $this->commands([
            \Torskint\ThemeGenerator\Console\Commands\GenerateThemeCommand::class,
            \Torskint\ThemeGenerator\Console\Commands\GenerateImageThemeCommand::class,
        ]);
    }
}
