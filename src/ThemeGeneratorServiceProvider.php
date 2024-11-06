<?php

namespace Torskint\ThemeGenerator;

use Illuminate\Support\ServiceProvider;
use Torskint\ThemeGenerator\Console\Commands\GenerateThemeCommand;

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
        $this->commands([
            \Torskint\ThemeGenerator\Console\Commands\GenerateThemeCommand::class,
        ]);
    }
}
