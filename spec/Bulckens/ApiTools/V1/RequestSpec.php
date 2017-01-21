<?php

namespace spec\Bulckens\ApiTools\V1;

use Bulckens\ApiTools\V1\Request;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RequestSpec extends ObjectBehavior {

  function let() {
    $this->beConstructedWith( 'test', 'generic' );
  }

  function it_stores_the_given_source_key() {
    $this->source()->shouldBe( 'http://fake.zwartopwit.be' );
  }

  function it_stores_the_given_api_secret_key() {
    $this->secret()->shouldStartWith( '12345678910111213' );
  }

  function it_adds_the_api_v1_namespace_to_the_path() {
    $this->path()->shouldBe( '/api/v1' );
  }

}
