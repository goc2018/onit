<?php

namespace App\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use OnIt\PythonBackend\Logic\PythonBackendLogic;
use OnIt\PythonBackend\Repository\PythonBackendRepository;

class PythonBackendLogicServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(PythonBackendLogic::class, function () {
            return new PythonBackendLogic(
                new PythonBackendRepository(
                    new Client(
                        [
                            'base_uri' => PythonBackendRepository::BASE_URI
                        ]
                    )
                )
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
