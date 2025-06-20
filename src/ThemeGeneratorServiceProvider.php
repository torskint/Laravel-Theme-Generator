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
        // Enregistrer les commandes Artisan
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateThemeCommand::class,
                GenerateImageThemeCommand::class,
            ]);

            // Publier le fichier de configuration
            $this->publishes([
                __DIR__.'/../config/torskint-theme-generator.php' => config_path('torskint-theme-generator.php'),
            ], 'config');
        }

        // Charger les helpers
        $this->loadHelpers();

        // Middleware (décommenter si besoin)
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
        // Bien vérifier le chemin ici (un niveau en dessous par rapport à boot)
        $this->mergeConfigFrom(
            __DIR__.'/../config/torskint-theme-generator.php',
            'torskint-theme-generator'
        );
    }
}
