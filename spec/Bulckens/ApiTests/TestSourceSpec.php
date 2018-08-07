<?php

namespace spec\Bulckens\ApiTests;

use Bulckens\AppTools\App;
use Bulckens\ApiTools\Api;
use Bulckens\ApiTests\TestSource;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TestSourceSpec extends ObjectBehavior {

  function let() {
    $app = new App( 'dev', __DIR__, 3 );
    $app->module( 'api', new Api() );
  }


  // Get method
  function it_returns_a_source_url_for_the_corresponding_given_key() {
    $this::get( 'fake' )->shouldBe( 'http://fake.zwartopwit.be' );
  }

  function it_returns_a_source_for_the_corresponding_given_key_using_the_dynamic_source_method() {
    $api = Api::get();
    $api->configFile( 'api.source.yml' );
    $this::get( 'alternative' )->shouldBe( 'http://alternative.com' );
    $api->configFile( 'api.yml' );
  }

  function it_fails_if_the_defined_source_method_is_not_callable() {
    $api = Api::get();
    $api->configFile( 'api.source_fail.yml' );
    $this::shouldThrow( 'Bulckens\ApiTools\SourceMethodNotCallableException' )->duringGet( 'generic' );
    $api->configFile( 'api.yml' );
  }

  function it_returns_null_if_the_corresponding_value_could_not_be_found() {
    $this::get( 'mastaba' )->shouldBe( null );
  }

}