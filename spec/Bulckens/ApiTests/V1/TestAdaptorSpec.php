<?php

namespace spec\Bulckens\ApiTests\V1;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;
use Bulckens\AppTools\App;
use Bulckens\ApiTools\V1\Api;
use Bulckens\ApiTests\V1\TestAdaptor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TestAdaptorSpec extends ObjectBehavior {

  protected $req;
  protected $res;
  protected $args = [
    'format' => 'json'
  ];

  function let() {
    new App( 'dev', __DIR__, 4 );
    new Api( 'fake' );

    $environment = Environment::mock([
      'REQUEST_URI' => '/fake.json'
    ]);
    $this->req = Request::createFromEnvironment( $environment );
    $this->res = new Response( 200 );
  }

  // Action method
  function it_returns_a_closure_for_slim() {
    $this->action( 'index' )->shouldBeCallable();
  }

  function it_returns_a_slim_response_when_called() {
    $action = $this->action( 'index' );
    $action( $this->req, $this->res, $this->args )->shouldHaveType( 'Slim\Http\Response' );
  }

  function it_returns_http_code_200_when_called() {
    $action = $this->action( 'index' );
    $action( $this->req, $this->res, $this->args )->getStatusCode()->shouldBe( 200 );
  }

}
