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

}
