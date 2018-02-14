<?php

namespace spec\Bulckens\ApiTools;

use Bulckens\AppTools\App;
use Bulckens\ApiTools\Api;
use Bulckens\ApiTools\RawToken;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RawTokenSpec extends ObjectBehavior {
  
  function let() {
    $app = new App( 'dev', __DIR__, 3 );
    $app->module( 'api', new Api() );
    $app->run();
  }


  // Initialization
  function it_fails_when_no_secret_could_be_found() {
    $this
      ->shouldThrow( 'Bulckens\AppTools\Traits\TokenishSecretMissingException' )
      ->during__construct( '/trouble.json', null );
  }


  // Secret method
  function it_returns_the_secret_for_a_given_key() {
    $this->beConstructedWith( '/fake.json', '123456789101112131415161718' );
    $this->secret()->shouldBe( '123456789101112131415161718' );
  }

}
