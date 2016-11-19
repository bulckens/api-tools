<?php 

namespace Bulckens\ApiTools\V1;

class Request extends Model {

  // Initialize new instance
  public function __construct() {
    $this->uri = '/api/v1';
  }

  // Initialize entity
  public function entity( $entity ) {
    return new Entity( $this->add( $entity ) );
  }

}