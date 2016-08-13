<?php

namespace Inspheric\Support\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Inspheric\Support\Traits\PackageProvider;

use Closure;

abstract class PackageServiceProvider extends ServiceProvider
{
    use PackageProvider;

    /**
    * This namespace is applied to your controller routes.
    *
    * In addition, it is set as the URL generator's root namespace.
    *
    * @var string
    */
    protected $namespace = 'Http\Controllers';

    /**
    * Define your route model bindings, pattern filters, etc.
    *
    * @return void
    */
    public function boot()
    {
        $this->bootRoutes();
        $this->bootMigrations();
        $this->bootTranslations();
        $this->bootViews();

    }

    protected function notOverridden($type)
    {
        return isset($this->$type) ? $this->$type !== false : true;
    }

    /**
    * Register the package services.
    *
    * @return void
    */
    public function register()
    {
        $this->registerConfig();
    }

    /**
    * Register the package config.
    *
    * @return void
    */
    public function registerConfig()
    {
        if ($this->notOverridden('config')) {
            $this->mergeConfigFrom($this->basePath('config/'.$this->name.'.php'), $this->name);
        }
    }

    /**
    * Register the files published by the package.
    *
    * @return void
    */
    public function bootPublishes()
    {
        if ($this->notOverridden('config')) {
            $this->publishes([
                $this->basePath('config/'.$this->name.'.php') => config_path($this->name.'.php'),
            ], 'config');
        }

        if ($this->notOverridden('translations')) {
            $this->publishes([
                $this->basePath('resources/lang') => resource_path('lang/vendor/'.$this->name),
            ], 'translations');
        }

        if ($this->notOverridden('views')) {
            $this->publishes([
                $this->basePath('resources/views') => resource_path('views/vendor/'.$this->name),
            ], 'views');
        }
    }

    /**
    * Register the package migrations.
    *
    * @return void
    */
    public function bootMigrations()
    {
        if ($this->notOverridden('migrations')) {
            $this->loadMigrationsFrom($this->basePath('database/migrations'));
        }
    }

    /**
    * Register the package views.
    *
    * @return void
    */
    public function bootViews()
    {
        if ($this->notOverridden('views')) {
            $this->loadViewsFrom($this->basePath('resources/views'), $this->name);
        }
    }

    /**
    * Register the package translations.
    *
    * @return void
    */
    public function bootTranslations()
    {
        if ($this->notOverridden('translations')) {
            $this->loadTranslationsFrom($this->basePath('resources/lang/'), $this->name);
        }
    }

    /**
    * Define the routes for the package.
    *
    * @return void
    */
    public function bootRoutes()
    {
        if ($this->notOverridden('routes')) {

            if (method_exists($this, 'bindRoutes')) {
                $this->bindRoutes();
            }

            if (! $this->app->routesAreCached()) {
                $this->mapWebRoutes();
                $this->mapApiRoutes();
            }
        }
    }

    public function bindModel($key, $class, Closure $callback = null)
    {
        Route::model($key, $class, $callback);
    }

    public function binding($key, $binder)
    {
        Route::bind($key, $binder);
    }

    /**
    * Define the "web" routes for the package.
    *
    * These routes all receive session state, CSRF protection, etc.
    *
    * @return void
    */
    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require $this->basePath('routes/web.php');
        });
    }

    /**
    * Define the "api" routes for the package.
    *
    * These routes are typically stateless.
    *
    * @return void
    */
    protected function mapApiRoutes()
    {
        Route::group([
            'middleware' => ['api', 'auth:api'],
            'namespace' => $this->namespace,
            'prefix' => 'api',
        ], function ($router) {
            require $this->basePath('routes/api.php');
        });
    }

}
