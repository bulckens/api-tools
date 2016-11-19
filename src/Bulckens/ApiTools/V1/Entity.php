<?php 

namespace Bulckens\ApiTools\V1;

class Entity extends Model {

  // Initialize new instance
  public function __construct( $part ) {
    $this->register( $part );
  }

  // Initialize resource
  public function resource( $resource ) {
    return new Resource( $this->add( $resource ) );
  }

}