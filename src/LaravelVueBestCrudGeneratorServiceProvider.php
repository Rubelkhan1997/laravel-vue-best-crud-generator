<?php

namespace Rubel\LaravelVueBestCrudGenerator;

use Illuminate\Support\ServiceProvider;
use Rubel\LaravelVueBestCrudGenerator\Commands\MakeCrudModule;
use Rubel\LaravelVueBestCrudGenerator\Commands\PublishFrontendAssets;
use Rubel\LaravelVueBestCrudGenerator\Commands\SetupAuth;

class LaravelVueBestCrudGeneratorServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            // Publish stubs
            $this->publishes([
                __DIR__ . '/../stubs' => base_path('stubs/laravel-vue-best-crud-generator'),
            ], 'laravel-vue-best-crud-generator-stubs');

            // Publish frontend assets
            $this->publishes([
                __DIR__ . '/../stubs-frontend' => base_path('stubs/laravel-vue-best-crud-generator-frontend'),
            ], 'laravel-vue-best-crud-generator-frontend');

            // Publish config
            $this->publishes([
                __DIR__ . '/../config/laravel-vue-best-crud-generator.php' => config_path('laravel-vue-best-crud-generator.php'),
            ], 'laravel-vue-best-crud-generator-config');

            // Register commands
            $this->commands([
                MakeCrudModule::class,
                PublishFrontendAssets::class,
                SetupAuth::class,
            ]);
        }
    }

    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-vue-best-crud-generator.php',
            'laravel-vue-best-crud-generator'
        );
    }
}
