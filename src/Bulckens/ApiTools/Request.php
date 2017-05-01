<?php 

namespace Bulckens\ApiTools;

class Request extends Model {

  // Initialize new instance
  public function __construct( $source, $secret = 'generic' ) {
    $this->secret( $secret )->source( $source );
  }

}