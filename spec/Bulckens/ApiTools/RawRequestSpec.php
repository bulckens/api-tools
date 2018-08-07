<?php

namespace spec\Bulckens\ApiTools;

use Bulckens\AppTools\App;
use Bulckens\ApiTools\Api;
use Bulckens\ApiTools\RawRequest;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RawRequestSpec extends ObjectBehavior {

  function let() {
    $app = new App( 'dev', __DIR__, 3 );
    $app->module( 'api', new Api() );
    $app->run();

    $this->beConstructedWith( 'http://fake.zwartopwit.be', '12345678910111213' );
  }

  function it_allows_to_be_contructed_without_a_secret() {
    $this->beConstructedWith( 'http://fake.zwartopwit.be' );
    $this->secret()->shouldBe( null );
  }

  function it_stores_the_given_source_key() {
    $this->source()->shouldBe( 'http://fake.zwartopwit.be' );
  }

  function it_stores_the_given_api_secret_key() {
    $this->secret()->shouldStartWith( '12345678910111213' );
  }

  function it_retruns_a_base_path() {
    $this->path()->shouldBe( '/' );
  }


  // Source method
  function it_returns_the_source_server() {
    $this->source( 'http://fake.zwartopwit.be' )->resource( 'path' );
    $this->source()->shouldBe( 'http://fake.zwartopwit.be' );
  }

  function it_sets_the_source_server() {
    $this->source( 'http://fake.zwartopwit.be' )->source()->shouldBe( 'http://fake.zwartopwit.be' );
  }

  function it_sets_the_source_server_after_registering_a_resource() {
    $this->secret( '12345678910111213' )->resource( 'path' )->source( 'http://fake.zwartopwit.be' );
    $this->source()->shouldBe( 'http://fake.zwartopwit.be' );
    $this->url()->shouldStartWith( 'http://fake.zwartopwit.be/path.json?token=' );
  }

  function it_returns_itself_after_setting_the_source_server() {
    $this->source( 'http://fake.zwartopwit.be' )->shouldBe( $this );
  }


  // Secret method
  function it_sets_a_given_secret_and_returns_the_stored_secret() {
    $this->secret( '12345678910111213' )->secret()->shouldStartWith( '12345678910111213' );
  }

  function it_sets_the_given_secret_and_retuns_itself() {
    $this->secret( '12345678910111213' )->shouldBe( $this );
  }

  function it_returns_nothing_if_no_secret_is_defined() {
    $this->beConstructedWith( 'http://fake.zwartopwit.be' );
    $this->secret()->shouldBe( null );
  }

}
