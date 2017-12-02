<?php

namespace Bulckens\ApiTools\Interfaces;

interface WrapperInterface {

  public function __construct( $body );

  public function __toString();

  public function getBody();

}