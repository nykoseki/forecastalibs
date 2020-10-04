<?php

namespace Forecasta\Laravel;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;


class JavaScriptServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Forecasta', function ($app) {
            /*
            return new Transformer(
                new LaravelViewBinder($app['events'], config('javascript.bind_js_vars_to_this_view')),
                config('javascript.js_namespace')
            );
            */
            return new ForecastaMain();
        });


        $this->mergeConfigFrom(
            __DIR__ . '/../config/forecasta.php', 'forecasta'
        );

    }

    /**
     * Publish the plugin configuration.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/forecasta.php' => config_path('forecasta.php')
        ]);

        if (class_exists('Illuminate\Foundation\AliasLoader')) {
            AliasLoader::getInstance()->alias(
                'ForeCasta',
                'Forecasta\Laravel\ForecastaServiceFacade'
            );
        } else {
            class_alias('Forecasta\Laravel\ForecastaServiceFacade', 'ForeCasta');
        }
    }

}
