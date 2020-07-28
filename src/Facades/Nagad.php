<?php

namespace Code4mk\Nagad\Facades;

/**
 * Nagad Facades
 * @author code4mk <hiremostafa@gmail.com>
 * @version 1.0.0
 */

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
