<?php 

namespace Bulckens\ApiTools\V1;

use Bulckens\AppTools\Traits\Modulized;
use Bulckens\AppTools\Traits\Configurable;

class Api {

  use Modulized;
  use Configurable;

  public function __construct( $source, $secret = 'generic' ) {
    self::$instance = $this;

    // create request module
    $this->module( 'request', new Request( $source, $secret ) );
  }


  // Get request instance
  public function request() {
    return $this->module( 'request' );
  }

}