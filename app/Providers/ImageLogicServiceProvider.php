<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OnIt\Image\Logic\ImageLogic;
use OnIt\PythonBackend\Logic\PythonBackendLogic;

class ImageLogicServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(ImageLogic::class, function ($app) {
            return new ImageLogic(
                $app[PythonBackendLogic::class]
            );
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
