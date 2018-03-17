<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OnIt\Image\Logic\ImageLogic;
use OnIt\Registration\Logic\RegistrationLogic;
use OnIt\Registration\Repository\RegistrationRepository;

class RegistrationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
	    $this->app->singleton(RegistrationLogic::class, function($app){
	    	return new RegistrationLogic(
	    	    $app[ImageLogic::class],
	    		new RegistrationRepository()
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
    }
}
