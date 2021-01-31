<?php


namespace App\Providers;

use App\Repository\ShukkinRepository;
use App\RepositoryInterface\ShukkinRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class ShukkinServiceProvider extends  ServiceProvider
{
    public function register()
    {
        app()->singleton(ShukkinRepositoryInterface::class,function(){
            return new ShukkinRepository();
        });
    }
}
