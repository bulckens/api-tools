<?php 

namespace Bulckens\ApiTools\V1;

use Bulckens\AppTools\Traits\Configurable;

class Api {

  use Configurable;

  protected static $instance; 

  public function __construct() {
    self::$instance = $this;
  }


  // Get request instance
  public function request( $source, $secret = 'generic' ) {
    return new Request( $source, $secret );
  }


  // Get instance
  public static function get() {
    return self::$instance;
  }

}