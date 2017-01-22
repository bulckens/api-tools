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

  function it_creates_a_new_request_with_the_default_secret() {
    $this::request( 'test' )->secret()->shouldBe( '1234567891011121314151617181920212223242526272829303132333435363' );
  }

  function it_creates_a_new_request_with_the_given_secret() {
    $this::request( 'test', 'reverse' )->secret()->shouldBe( '3635343332313039282726252423222120291817161514131211101987654321' );
  }

}
