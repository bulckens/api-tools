<?php

namespace spec\Bulckens\ApiTools;

use Bulckens\Helpers\TimeHelper;
use Bulckens\AppTools\App;
use Bulckens\ApiTools\Api;
use Bulckens\ApiTools\Token;
use Bulckens\ApiTools\Secret;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TokenSpec extends ObjectBehavior {
  
  function let() {
    $app = new App( 'dev', __DIR__, 3 );
    $app->module( 'api', new Api() );
    $app->run();
  }


  // Initialization
  function it_fails_when_no_secret_could_be_found() {
    $this
      ->shouldThrow( 'Bulckens\AppTools\Traits\TokenishSecretMissingException' )
      ->during__construct( '/trouble.json', 'phish' );
  }


  // Get method
  function it_returns_the_generated_token() {
    $this->beConstructedWith( '/feel/fladdr.json', 'generic' );
    $this->get()->shouldMatch( '/^[a-z0-9]{75}$/' );
  }

  function it_generates_a_valid_token() {
    $uri    = '/tasty/bears.xml';
    $stamp  = round( microtime( 1 ) * 1000 );
    $secret = Secret::get( 'generic' );
    $token  = hash( 'sha256', implode( '---', [ $secret, $stamp, $uri ] ) ) . dechex( $stamp );

    $this->beConstructedWith( $uri, 'generic', $stamp );
    $this->get()->shouldBe( $token );
  }


  // Uri method
  function it_returns_the_given_uri() {
    $this->beConstructedWith( '/feel/fladdr.json', 'generic' );
    $this->uri()->shouldBe( '/feel/fladdr.json' );
  }


  // Secret method
  function it_returns_the_secret_for_a_given_key() {
    $this->beConstructedWith( '/fake.json', 'generic' );
    $this->secret()->shouldBe( '1234567891011121314151617181920212223242526272829303132333435363' );
  }


  // Stamp method
  function it_returns_the_given_timestamp() {
    $stamp = TimeHelper::ms();
    $this->beConstructedWith( '/fake.json', 'generic', $stamp );
    $this->stamp()->shouldBe( $stamp );
  }

  function it_returns_the_generated_timestamp() {
    $this->beConstructedWith( '/fake.json', 'generic' );
    $this->stamp()->shouldBeDouble();
  }


  // Validate method
  function it_verifies_the_validity_of_a_token() {
    $stamp = TimeHelper::ms();
    $token = new Token( '/fake.json', 'generic', $stamp );
    $this->beConstructedWith( '/fake.json', 'generic', $stamp );
    $this->validate( $token->get() )->shouldBe( true );
  }

  function it_verifies_the_validity_of_a_token_using_its_timestamp() {
    $stamp = TimeHelper::ms();
    $token = new Token( '/fake.json', 'generic', $stamp );
    $this->beConstructedWith( '/fake.json', 'generic' );
    $this->validate( $token->get() )->shouldBe( true );
  }

  function it_verifies_the_validity_of_a_token_using_its_timestamp_only_if_no_stamp_was_given() {
    $stamp = TimeHelper::ms();
    $token = new Token( '/fake.json', 'generic', $stamp );
    $this->beConstructedWith( '/fake.json', 'generic', $stamp + 100 );
    $this->validate( $token->get() )->shouldBe( false );
  }

  function it_verifies_the_invalidity_of_a_token() {
    $stamp = TimeHelper::ms();
    $token = new Token( '/fake.json', 'reverse', $stamp );
    $this->beConstructedWith( '/fake.json', 'generic', $stamp );
    $this->validate( $token->get() )->shouldBe( false );
  }


  // Timestamp static method
  function it_parses_a_given_token_into_token_and_timestamp() {
    $stamp = intval( TimeHelper::ms() );
    $token = new Token( '/fake.json', 'generic', $stamp );
    $this->beConstructedWith( '/fake.json', 'generic', $stamp );
    $this::timestamp( $token->get() )->shouldBe( $stamp );
  }

}
