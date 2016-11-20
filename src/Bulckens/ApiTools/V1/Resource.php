<?php 

namespace Bulckens\ApiTools\V1;

class Resource extends Model {

  // Initialize new instance
  public function __construct( $part ) {
    $this->register( $part );
  }

}