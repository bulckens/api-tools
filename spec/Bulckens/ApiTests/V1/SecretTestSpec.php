<?php

namespace spec\Bulckens\ApiTests\V1;

use Bulckens\ApiTests\V1\SecretTest;
use Bulckens\ApiTools\V1\Config;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SecretTestSpec extends ObjectBehavior {

  // Get method
  function it_returns_a_secret_for_the_corresponding_given_key() {
    $this::get( 'generic' )->shouldStartWith( '12345678910111213' );
  }

  function it_returns_a_secret_for_the_corresponding_given_key_using_the_dynamic_secret_method() {
    Config::file( 'api_tools.secret.yml' );
    $this::get( 'generic' )->shouldBe( 'fallalifallala' );
    Config::file( 'api_tools.yml' );
  }

  function it_fails_if_the_defined_secret_method_is_not_callable() {
    Config::file( 'api_tools.secret_fail.yml' );
    $this::shouldThrow( 'Bulckens\ApiTools\V1\SecretMethodNotCallableException' )->duringGet( 'generic' );
    Config::file( 'api_tools.yml' );
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