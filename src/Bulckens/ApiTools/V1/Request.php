<?php 

namespace Bulckens\ApiTools\V1;

class Request extends Model {

  // Initialize new instance
  public function __construct( $source ) {
    $this->uri = "$source:/api/v1";
  }

}