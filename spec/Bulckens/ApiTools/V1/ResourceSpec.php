<?php

namespace spec\Bulckens\ApiTools\V1;

use Bulckens\ApiTools\V1\Resource;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResourceSpec extends ObjectBehavior {

  function let() {
    $this->beConstructedWith( 'test:/flyers' );
  }
  
  function it_stores_the_given_uri() {
    $this->uri( 'path' )->shouldBe( '/flyers' );
  }

}
