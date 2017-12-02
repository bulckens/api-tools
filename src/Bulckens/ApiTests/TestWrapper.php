<?php

namespace Bulckens\ApiTests;

use Bulckens\ApiTools\Interfaces\WrapperInterface;

class TestWrapper implements WrapperInterface {

  protected $body;

  public function __construct( $body ) {
    $this->body = $body;
  }

  public function __toString() {
    return $this->getBody();
  }

  public function getBody() {
    return $this->body;
  }

}
