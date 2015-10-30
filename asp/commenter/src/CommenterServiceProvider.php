<?php

namespace asp\commenter;

use Illuminate\Support\ServiceProvider;

class CommenterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views','commenter');
        $this->publishes([
            __DIR__.'/CommenterConfig.php'=>config_path('CommenterConfig.php'),
            ]);
        $this->publishes([
            __DIR__.'/views/css'=>public_path('asp/res/css'),
            ]);
        $this->publishes([
            __DIR__.'/views/js'=>public_path('asp/res/js'),
            ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('asp\commenter\CommentController');
        include __DIR__.'/routes.php';
        $this->app->bind('asp\commenter\Helpers\Contracts\CommenterContract',function(){
            return new Commenter();
        });
        $this->app->bind('commenter',function(){
            return new Commenter();
        });
    }
}
