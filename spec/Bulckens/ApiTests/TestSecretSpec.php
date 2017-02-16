<?php

namespace spec\Bulckens\ApiTests;

use Bulckens\AppTools\App;
use Bulckens\ApiTools\Api;
use Bulckens\ApiTests\TestSecret;
use Bulckens\ApiTools\Config;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TestSecretSpec extends ObjectBehavior {

  function let() {
    $app = new App( 'dev', __DIR__, 3 );
    $app->module( 'api', new Api() );
  }

  // Get method
  function it_returns_a_secret_for_the_corresponding_given_key() {
    $this::get( 'generic' )->shouldStartWith( '12345678910111213' );
  }

  function it_returns_a_secret_for_the_corresponding_given_key_using_the_dynamic_secret_method() {
    $api = Api::get();
    $api->file( 'api.secret.yml' );
    $this::get( 'generic' )->shouldBe( 'fallalifallala' );
    $api->file( 'api.yml' );
  }

  function it_fails_if_the_defined_secret_method_is_not_callable() {
    $api = Api::get();
    $api->file( 'api.secret_fail.yml' );
    $this::shouldThrow( 'Bulckens\ApiTools\SecretMethodNotCallableException' )->duringGet( 'generic' );
    $api->file( 'api.yml' );
  }


  // Exists method
  function it_tests_positive_for_existance_of_a_key() {
    $this::exists( 'generic' )->shouldBe( true );
  }

  function it_tests_negative_for_non_existance_of_a_key() {
    $this::exists( 'kalumbana' )->shouldBe( false );
  }

  function it_tests_positive_for_existance_of_all_in_multiple_keys() {
    $this::exists([ 'generic', 'reverse' ])->shouldBe( true );
  }

  function it_tests_negative_for_existance_of_some_in_multiple_keys() {
    $this::exists([ 'generic', 'mastaba' ])->shouldBe( false );
  }

  function it_tests_negative_for_non_existance_of_all_in_multiple_keys() {
    $this::exists([ 'flamanca', 'mastaba' ])->shouldBe( false );
  }

}