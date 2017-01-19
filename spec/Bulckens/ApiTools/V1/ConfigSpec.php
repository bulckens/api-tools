<?php

namespace spec\Bulckens\ApiTools\V1;

use Bulckens\ApiTools\V1\Config;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ConfigSpec extends ObjectBehavior {

  // Initialization
  function it_parses_the_config_file() {
    $this::get( 'methods' )->shouldBeArray();
    $this::get( 'verbose' )->shouldBe( true );
  }

  function it_fails_when_the_environment_method_is_not_defined() {
    self::file( 'api_tools.no_env.yml' );
    $this->shouldThrow( 'Bulckens\ApiTools\V1\MissingEnvMethodException' )->during( '__construct' );
    self::file( 'api_tools.yml' );
  }

  function it_fails_when_the_environment_method_is_not_callable() {
    self::file( 'api_tools.bad_env.yml' );
    $this->shouldThrow( 'Bulckens\ApiTools\V1\EnvMethodNotCallableException' )->during( '__construct' );
    self::file( 'api_tools.yml' );
  }

  function it_fails_when_the_environment_is_not_defined() {
    self::file( 'api_tools.missing.yml' );
    $this->shouldThrow( 'Bulckens\ApiTools\V1\MissingEnvConfigException' )->during( '__construct' );
    self::file( 'api_tools.yml' );
  }


  // Get method
  function it_gets_a_value_for_a_plain_key() {
    $this::get( 'verbose' )->shouldBe( true );
  }

  function it_gets_a_value_for_nested_keys() {
    $this::get( 'secrets.generic' )->shouldStartWith( '1234567891011121314' );
  }

  function it_returns_null_for_non_existant_keys() {
    $this::get( 'falaba.minusta' )->shouldBe( null );
  }


  // Exists method
  function it_tests_positive_for_existing_configs() {
    $this::exists()->shouldBe( true );
  }

  function it_fails_with_missing_config_file() {
    self::file( 'who.are.you' );
    $this::shouldThrow( 'Bulckens\ApiTools\V1\MissingConfigException' )->duringExists();
    self::file( 'api_tools.yml' );
  }


  // File method
  function it_gets_the_config_file_path() {
    $this::file()->shouldEndWith( 'config/api_tools.yml' );
  }

  function it_sets_the_config_file_path() {
    $this::file( 'api_tools.render.yml' )->shouldBe( null );
    $this::file()->shouldEndWith( 'config/api_tools.render.yml' );
    self::file( 'api_tools.yml' );
  }


  // Root method
  function it_gets_the_root_path() {
    $this::root()->shouldEndWith( 'api-tools/' );
  }

  function it_gets_the_root_path_adding_the_given_path() {
    $this::root( '/falama/sinoka/male.fsg' )->shouldEndWith( 'api-tools/falama/sinoka/male.fsg' );
  }


  // Env method
  function it_gets_the_current_environment() {
    $this::env()->shouldBe( 'test' );
  }

  function it_tests_positive_with_the_given_environment_against_the_current_environment() {
    $this::env( 'test' )->shouldBe( true );
  }

  function it_tests_negative_with_the_given_environment_against_the_current_environment() {
    $this::env( 'falalalalala' )->shouldBe( false );
  }

}

























