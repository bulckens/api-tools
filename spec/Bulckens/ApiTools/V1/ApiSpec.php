<?php

namespace spec\Bulckens\ApiTools\V1;

use Bulckens\ApiTools\V1\Api;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ApiSpec extends ObjectBehavior {
  
  function it_creates_a_new_request_instance() {
    $this::request( 'test', 'generic' )->shouldHaveType( 'Bulckens\\ApiTools\\V1\\Request' );
  }

  function it_creates_a_new_request_with_the_given_source() {
    $this::request( 'test', 'generic' )->source()->shouldBe( 'http://fake.zwartopwit.be' );
  }

  function it_creates_a_new_request_with_the_given_secret() {
    $this::request( 'test', 'generic' )->secret()->shouldStartWith( '12345678910111213' );
  }

}
