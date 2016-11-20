<?php 

namespace Bulckens\ApiTools\V1;

class Entity extends Model {

  // Initialize new instance
  public function __construct( $part ) {
    $this->register( $part );
  }

  // Add resource
  public function resource( $resource, $id = null ) {
    return new Resource( $this->add( $resource, $id ) );
  }

}