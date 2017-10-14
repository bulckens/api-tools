<?php

namespace spec\Bulckens\ApiTools;

use Bulckens\AppTools\App;
use Bulckens\ApiTools\Api;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ApiSpec extends ObjectBehavior {

  function let() {
    new App( 'dev', __DIR__, 3 );
  }

  // Request method
  function it_returns_the_request_instance() {
    $this->request( 'fake' )->shouldHaveType( 'Bulckens\ApiTools\Request' );
  }

  function it_creates_a_new_request_with_the_given_source() {
    $this->request( 'fake' )->source()->shouldBe( 'http://fake.zwartopwit.be' );
  }

  function it_creates_a_new_request_with_the_default_secret() {
    $this->request( 'fake' )->secret()->shouldBe( '1234567891011121314151617181920212223242526272829303132333435363' );
  }

  function it_creates_a_new_request_with_the_given_secret() {
    $this->request( 'fake', 'reverse' )->secret()->shouldBe( '3635343332313039282726252423222120291817161514131211101987654321' );
  }


  // Config method
  function it_returns_the_config_instance_without_an_argument() {
    $this->config()->shouldHaveType( 'Bulckens\AppTools\Config' );
  }

  function it_returns_the_the_value_for_a_given_key() {
    $this->config( 'secrets' )->shouldBeArray();
  }

  function it_returns_a_given_default_value_if_key_is_not_existing() {
    $this->config( 'flalalala', 500 )->shouldBe( 500 );
  }


  // File method
  function it_builds_config_file_name_from_class() {
    $this->configFile()->shouldBe( 'api.yml' );
  }

  function it_defines_a_custom_config_file() {
    $this->configFile( 'api.custom.yml' );
    $this->configFile()->shouldBe( 'api.custom.yml' );
    $this->config( 'custom' )->shouldBe( 'wichtig' );
  }

  function it_unsets_the_custom_config_file_with_null_given() {
    $this->configFile( 'api.custom.yml' );
    $this->configFile()->shouldBe( 'api.custom.yml' );
    $this->configFile( null );
    $this->configFile()->shouldBe( 'api.yml' );
  }

  function it_returns_itself_after_defining_a_custom_config_file() {
    $this->configFile( 'api.custom.yml' )->shouldBe( $this );
  }


  // Get method
  function it_references_the_app_instance() {
    $this::get()->shouldBe( $this );
  }

}
