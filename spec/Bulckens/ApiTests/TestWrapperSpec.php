<?php

namespace spec\Bulckens\ApiTests;

use Bulckens\ApiTests\TestWrapper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TestWrapperSpec extends ObjectBehavior {
  
  // ToBody method
  function it_returns_the_body() {
    $body = '<div>Clara</div>';
    $this->beConstructedWith( $body );
    $this->getBody()->shouldBe( $body );
  }

  // ToString method
  function it_is_convertable_to_string() {
    $this->beConstructedWith( '<div>Fara</div>' );
    $this->getBody()->shouldBe( sprintf( $this->getWrappedObject() ) );
  }

}
