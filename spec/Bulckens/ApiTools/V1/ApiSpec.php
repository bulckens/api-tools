<?php

namespace spec\Bulckens\ApiTools\V1;

use Bulckens\ApiTools\V1\Api;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ApiSpec extends ObjectBehavior {
  
  function it_creates_a_new_request_instance() {
    $this->request( 'test' )->shouldHaveType( 'Bulckens\\ApiTools\\V1\\Request' );
  }

}
