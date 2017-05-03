<?php 

namespace Bulckens\ApiTools;

class Request extends Model {

  // Initialize new instance
  public function __construct( $source, $secret = null ) {
    $this->source( $source );

    if ( ! is_null( $secret ) )
      $this->secret( $secret );
  }

}