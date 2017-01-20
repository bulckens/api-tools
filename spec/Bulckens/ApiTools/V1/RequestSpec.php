<?php

namespace spec\Bulckens\ApiTools\V1;

use Bulckens\ApiTools\V1\Request;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RequestSpec extends ObjectBehavior {

  function let() {
    $this->beConstructedWith( 'location' );
  }

  function it_stores_the_given_source_key() {
    $this->part( 'source' )->shouldBe( 'location' );
  }

  function it_stores_the_given_path_key() {
    $this->part( 'path' )->shouldBe( 'api/v1' );
  }

}
