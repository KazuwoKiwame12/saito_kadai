<?php

namespace App\Providers;

use App\Repository\UpdateWorkerRepository;
use App\RepositoryInterface\UpdateWorkerRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class UpdateWorkerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        app()->singleton(UpdateWorkerRepositoryInterface::class,function(){
            return new UpdateWorkerRepository();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
