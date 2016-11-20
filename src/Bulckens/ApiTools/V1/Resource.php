<?php 

namespace Bulckens\ApiTools\V1;

class Resource extends Model {

  // Initialize new instance
  public function __construct( $part ) {
    $this->register( $part );
  }

  // Add resource
  public function resource( $resource, $id = null ) {
    return new Resource( $this->add( $resource, $id ) );
  }

}