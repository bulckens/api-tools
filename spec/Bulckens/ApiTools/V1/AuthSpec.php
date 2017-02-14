<?php

namespace spec\Bulckens\ApiTools\V1;

use Bulckens\AppTools\App;
use Bulckens\ApiTools\V1\Api;
use Bulckens\ApiTools\V1\Secret;
use Bulckens\ApiTools\V1\Auth;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AuthSpec extends ObjectBehavior {

  function let() {
    new App( 'dev', __DIR__, 4 );
    new Api( 'dev' );
  }

  // Token method
  function it_generates_a_token() {
    $this::token( '/tasty/bears.xml' )->shouldBeString();
    $this::token( '/tasty/bears.xml' )->shouldMatch( '/^[a-z0-9]{75}$/' );
  }

  function it_generates_token_accoring_to_algorithm() {
    $uri    = '/tasty/bears.xml';
    $stamp  = round( microtime( 1 ) * 1000 );
    $secret = Secret::get( 'generic' );
    $token  = hash( 'sha256', implode( '---', [ $secret, $stamp, $uri ] ) ) . dechex( $stamp );

    $this::token( $uri, $stamp )->shouldBe( $token );
  }

  function it_fails_when_the_secret_could_not_be_found() {
    $this::shouldThrow( 'Bulckens\ApiTools\V1\AuthMissingSecretException' )->duringToken( '/tasty/bears.xml', null, 'hihihi' );
  }


  // Stamp method
  function it_generates_a_timestamp() {
    $this::stamp()->shouldBeDouble();
  }
  
}
