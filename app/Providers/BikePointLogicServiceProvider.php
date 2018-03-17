<?php

namespace App\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use OnIt\BikePoint\Logic\BikePointLogic;
use OnIt\OpenData\BikePoint\Repository\BikePointRepository;

class BikePointLogicServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(BikePointLogic::class, function () {
            return new BikePointLogic(
                new BikePointRepository(
                    new Client()
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
