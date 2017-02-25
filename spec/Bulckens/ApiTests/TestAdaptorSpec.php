<?php

namespace spec\Bulckens\ApiTests;

use stdClass;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;
use Bulckens\AppTools\App;
use Bulckens\ApiTools\Api;
use Bulckens\ApiTests\TestAdaptor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TestAdaptorSpec extends ObjectBehavior {

  protected $req;
  protected $res;
  protected $args = [
    'format' => 'json'
  ];

  function let() {
    $app = new App( 'dev', __DIR__, 3 );
    $app->module( 'api', new Api() );
    $app->run();

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

  function it_stores_the_given_request_on_action() {
    $action = $this->action( 'index' );
    $this->req()->shouldBe( null );
    $action( $this->req, $this->res, $this->args );
    $this->req()->shouldBe( $this->req );
  }

  function it_stores_the_given_response_on_action() {
    $action = $this->action( 'index' );
    $this->res()->shouldBe( null );
    $action( $this->req, $this->res, $this->args );
    $this->res()->shouldBe( $this->res );
  }

  function it_stores_the_given_arguments_on_action() {
    $action = $this->action( 'index' );
    $this->args()->shouldBe( null );
    $action( $this->req, $this->res, $this->args );
    $this->args()->shouldHaveKeyWithValue( 'format', 'json' );
  }

  function it_creates_an_output_object_with_given_format_on_action() {
    $action = $this->action( 'index' );
    $action( $this->req, $this->res, $this->args );
    $this->output()->shouldHaveType( 'Bulckens\ApiTools\Output' );
    $this->output()->format()->shouldBe( 'json' );
  }

  function it_creates_an_output_object_and_adds_the_given_path_on_action() {
    $action = $this->action( 'index' );
    $action( $this->req, $this->res, $this->args );
    $this->output()->path()->shouldBe( '/fake.json' );
  }


  // Render method
  function it_returns_a_response_object_on_render() {
    $action = $this->action( 'index' );
    $action( $this->req, $this->res, $this->args );
    $this->render()->shouldHaveType( 'Slim\Http\Response' );
  }

  function it_adds_statistic_for_output_on_render() {
    $action = $this->action( 'index' );
    $action( $this->req, $this->res, $this->args );
    $this->render();
    $this->output()->toArray()->shouldHaveKey( 'statistics' );
  }

  function it_only_renders_allowed_formats() {
    $action = $this->action( 'index' );
    $action( $this->req, $this->res, $this->args );
    $this->render([ 'xml' ]);
    $this->output()->toArray()->shouldHaveKeyWithValue( 'error', 'format.not_accepted' );
    $this->output()->status()->shouldBe( 406 );
  }


  // Output method
  function it_returns_the_output_object() {
    $action = $this->action( 'index' );
    $action( $this->req, $this->res, $this->args );
    $this->output()->shouldHaveType( 'Bulckens\ApiTools\Output' );
  }


  // Req method
  function it_returns_a_request() {
    $this->req( $this->req )->req()->shouldBe( $this->req );  
  }

  function it_returns_itself_after_storing_a_request() {
    $this->req( $this->req )->shouldBe( $this );
  }

  function it_fails_when_the_given_object_is_not_a_request() {
    $this->shouldThrow( 'Bulckens\ApiTools\AdaptorRequestInvalidException' )->duringReq( new stdClass() );
  }


  // Res method
  function it_returns_a_response() {
    $this->res( $this->res )->res()->shouldBe( $this->res );  
  }

  function it_returns_itself_after_storing_a_response() {
    $this->res( $this->res )->shouldBe( $this );
  }

  function it_fails_when_the_given_object_is_not_a_response() {
    $this->shouldThrow( 'Bulckens\ApiTools\AdaptorResponseInvalidException' )->duringRes( new stdClass() );
  }


  // Args method
  function it_returns_arguments() {
    $this->args( $this->args )->args()->shouldHaveKeyWithValue( 'format', 'json' );
  }

  function it_returns_a_single_argument() {
    $this->args([ 'first' => 'second', 'third' => 'fourth' ]);
    $this->args( 'first' )->shouldBe( 'second' );
    $this->args( 'third' )->shouldBe( 'fourth' );
  }

  function it_returns_null_for_a_non_existing_argument_key() {
    $this->args([ 'first' => 'second', 'third' => 'fourth' ]);
    $this->args( 'fifth' )->shouldBe( null );
  }

  function it_only_accepts_an_array_as_value_to_be_stored() {
    $this->shouldThrow( 'Bulckens\ApiTools\AdaptorArgumentsInvalidException' )->duringArgs( new stdClass() );
  }

  function it_returns_itself_after_storing_arguments() {
    $this->args( $this->args )->shouldBe( $this );
  }


  // View method
  function it_renders_a_view() {
    $this->view( 'view.html.twig' )->shouldBe( 'test view' );
  }

  function it_renders_a_view_with_locals() {
    $this->view( 'view_with_locals.html.twig', [ 'nice' => 'expensive' ] )->shouldBe( 'Very expensive' );
  }



}





