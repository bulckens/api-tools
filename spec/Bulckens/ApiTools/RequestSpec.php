<?php

namespace spec\Bulckens\ApiTools;

use Bulckens\AppTools\App;
use Bulckens\ApiTools\Api;
use Bulckens\ApiTools\Request;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RequestSpec extends ObjectBehavior {

  function let() {
    $app = new App( 'dev', __DIR__, 3 );
    $app->module( 'api', new Api() );
    $app->run();

    $this->beConstructedWith( 'fake', 'generic' );
  }

  function it_allows_to_be_contructed_without_a_secret() {
    $this->beConstructedWith( 'fake' );
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

}
