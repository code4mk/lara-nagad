<?php

namespace Code4mk\Nagad;


use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class NagadServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
   public function boot()
   {
       AliasLoader::getInstance()->alias('NagadPayment', 'Code4mk\Nagad\Facades\Nagad');
   }

  /**
   * Register any application services.
   *
   * @return void
   */
   public function register()
   {
       $this->app->bind('nagad', function () {
           return new Nagad;
       });
   }
}
