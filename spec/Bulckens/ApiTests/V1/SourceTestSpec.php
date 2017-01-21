<?php

namespace spec\Bulckens\ApiTests\V1;

use Bulckens\ApiTests\V1\SourceTest;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SourceTestSpec extends ObjectBehavior {

  // Get method
  function it_returns_a_source_url_for_the_corresponding_given_key() {
    $this::get( 'test' )->shouldBe( 'http://fake.zwartopwit.be' );
  }

  function it_returns_null_if_the_corresponding_value_could_not_be_found() {
    $this::get( 'mastaba' )->shouldBe( null );
  }

}
