<?php 

namespace Bulckens\ApiTools\V1;

class Request extends Model {

  // Initialize new instance
  public function __construct( $source, $secret = 'generic' ) {
    $this->secret( $secret )->source( $source )->resource( 'api', 'v1' );
  }

}