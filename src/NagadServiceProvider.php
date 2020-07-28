<?php

namespace Code4mk\Nagad;

/**
 * Service Provider class
 * @author code4mk <hiremostafa@gmail.com>
 */

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
       $this->publishes([
        __DIR__ . '/../config/nagad.php' => config_path('nagad.php'),
      ], 'config');

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
