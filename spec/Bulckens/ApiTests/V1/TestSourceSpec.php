<?php

namespace spec\Bulckens\ApiTests\V1;

use Bulckens\AppTools\App;
use Bulckens\ApiTools\V1\Api;
use Bulckens\ApiTests\V1\TestSource;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TestSourceSpec extends ObjectBehavior {

  function let() {
    new App( 'dev', __DIR__, 4 );
    new Api( 'dev' );
  }


  // Get method
  function it_returns_a_source_url_for_the_corresponding_given_key() {
    $this::get( 'dev' )->shouldBe( 'http://fake.zwartopwit.be' );
  }

  function it_returns_null_if_the_corresponding_value_could_not_be_found() {
    $this::get( 'mastaba' )->shouldBe( null );
  }

}
