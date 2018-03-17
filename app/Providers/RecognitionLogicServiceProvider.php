<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OnIt\PythonBackend\Logic\PythonBackendLogic;
use OnIt\Recognition\Logic\RecognitionLogic;

class RecognitionLogicServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(RecognitionLogic::class, function ($app) {
            return new RecognitionLogic(
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
