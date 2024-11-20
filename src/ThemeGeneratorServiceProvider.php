<?php

namespace Torskint\ThemeGenerator;

use Illuminate\Support\ServiceProvider;

use Torskint\ThemeGenerator\Console\Commands\GenerateThemeCommand;
use Torskint\ThemeGenerator\Console\Commands\GenerateImageThemeCommand;
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
            __DIR__.'/../../config/torskint-theme-generator.php' => config_path('torskint-theme-generator.php'),
        ], 'config');

        // Enregistrement du middleware pour gérer le thème
        // $this->app['router']->pushMiddlewareToGroup('web', SetThemeMiddleware::class);
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
            \Torskint\ThemeGenerator\Console\Commands\GenerateImageThemeCommand::class,
        ]);
    }
}
