<?php

namespace App\Providers;

use App\Repository\ListRepository;
use App\RepositoryInterface\ListRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class ListServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        app()->singleton(ListRepositoryInterface::class,function(){
            return new ListRepository();
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
