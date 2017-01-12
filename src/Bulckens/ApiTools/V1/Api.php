<?php 

namespace Bulckens\ApiTools\V1;

class Api {

  // Build request
  public static function request( $source ) {
    return new Request( $source );
  }

}