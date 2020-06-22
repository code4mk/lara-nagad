<?php

namespace Code4mk\Nagad\Facades;

use Illuminate\Support\Facades\Facade;

class Nagad extends Facade
{
  /**
   * Get the registered name of the component.
   *
   * @return string
   */
  protected static function getFacadeAccessor()
  {
      return 'nagad';
  }
}
