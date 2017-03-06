<?php

namespace spec\Bulckens\ApiTests;

use stdClass;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;
use Bulckens\AppTools\App;
use Bulckens\ApiTools\Api;
use Bulckens\ApiTools\Output;
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

  function it_returns_an_instance_of_action() {
    $this->action( 'index' )->shouldHaveType( 'Bulckens\ApiTools\Action' );
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

  function it_uses_the_custom_defined_format() {
    $action = $this->action( 'index' );
    $action( $this->req, $this->res, [] )->getStatusCode()->shouldBe( 200 );
  }

  function it_fails_with_a_non_existent_action() {
    $this->shouldThrow( 'Bulckens\ApiTools\AdaptorActionMissingException' )->duringAction( 'shalala' );
  }


  // Render method
  function it_returns_an_action() {
    $this->action( 'index' )->shouldHaveType( 'Bulckens\ApiTools\Action' );
  }

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

  function it_adds_request_details_for_output_on_render() {
    $action = $this->action( 'index' );
    $action( $this->req, $this->res, $this->args );
    $this->render();
    $this->output()->toArray()->shouldHaveKey( 'request' );
  }

  function it_only_renders_allowed_formats() {
    $action = $this->action( 'index' );
    $action( $this->req, $this->res, $this->args );
    $this->render([ 'xml' ]);
    $this->output()->toArray()->shouldHaveKeyWithValue( 'error', 'format.not_accepted' );
    $this->output()->status()->shouldBe( 406 );
  }

  function it_renders_a_view() {
    $action = $this->action( 'index' );
    $action( $this->req, $this->res, $this->args );
    $response = $this->render( 'view.html.twig' );
    $response->shouldHaveType( 'Slim\Http\Response' );
  }

  function it_renders_a_view_with_locals() {
    $action = $this->action( 'index' );
    $action( $this->req, $this->res, [ 'format' => 'html' ] );
    $response = $this->render( 'view_with_locals.html.twig', [ 'nice' => 'expensive' ] );
    $response->shouldHaveType( 'Slim\Http\Response' );
    $response->getBody()->__toString()->shouldBe( 'Very expensive' );
  }

  function it_renders_a_view_with_extra_statistics_information() {
    $action = $this->action( 'index' );
    $action( $this->req, $this->res, [ 'format' => 'html' ] );
    $response = $this->render( 'Used time: {{ statistics.used_time }}' );
    $response->getBody()->__toString()->shouldMatch( '/^Used time\:\s\d+ms$/' );
  }

  function it_renders_a_view_with_extra_request_uri_information() {
    $action = $this->action( 'index' );
    $action( $this->req, $this->res, [ 'format' => 'html' ] );
    $response = $this->render( 'Request uri: {{ request.uri }}' );
    $response->getBody()->__toString()->shouldStartWith( 'Request uri: http://localhost/fake.json' );
  }

  function it_renders_a_view_with_extra_request_path_information() {
    $action = $this->action( 'index' );
    $action( $this->req, $this->res, [ 'format' => 'html' ] );
    $response = $this->render( 'Request path: {{ request.path }}' );
    $response->getBody()->__toString()->shouldStartWith( 'Request path: /fake.json' );
  }

  function it_renders_a_view_with_extra_request_scheme_information() {
    $action = $this->action( 'index' );
    $action( $this->req, $this->res, [ 'format' => 'html' ] );
    $response = $this->render( 'Request scheme: {{ request.scheme }}' );
    $response->getBody()->__toString()->shouldStartWith( 'Request scheme: http' );
  }

  function it_renders_a_view_with_extra_request_host_information() {
    $action = $this->action( 'index' );
    $action( $this->req, $this->res, [ 'format' => 'html' ] );
    $response = $this->render( 'Request host: {{ request.host }}' );
    $response->getBody()->__toString()->shouldStartWith( 'Request host: localhost' );
  }

  function it_renders_a_view_with_extra_request_port_information() {
    $action = $this->action( 'index' );
    $action( $this->req, $this->res, [ 'format' => 'html' ] );
    $response = $this->render( 'Request port: {{ request.port }}' );
    $response->getBody()->__toString()->shouldStartWith( 'Request port: ' );
  }

  function it_renders_a_view_with_extra_request_base_information() {
    $action = $this->action( 'index' );
    $action( $this->req, $this->res, [ 'format' => 'html' ] );
    $response = $this->render( 'Request base: {{ request.base }}' );
    $response->getBody()->__toString()->shouldStartWith( 'Request base: http://localhost' );
  }



  // Output method
  function it_returns_the_output_object() {
    $action = $this->action( 'index' );
    $action( $this->req, $this->res, $this->args );
    $this->output()->shouldHaveType( 'Bulckens\ApiTools\Output' );
  }

  function it_sets_a_new_output_object() {
    $this->output()->shouldBe( null );
    $this->output( new Output( 'json' ) );
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

  function it_returns_a_single_argument_with_fallback() {
    $this->args([ 'first' => 'second', 'third' => 'fourth' ]);
    $this->args( 'fifth', 'sixth' )->shouldBe( 'sixth' );
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


  // Info method
  function it_returns_an_array_with_request_information() {
    $action = $this->action( 'index' );
    $action( $this->req, $this->res, [ 'format' => 'html' ] );
    $info = $this->information();
    $info['request']->shouldBeArray();
    $info['request']['uri']->shouldBeString();
    $info['request']['base']->shouldBeString();
    $info['request']['scheme']->shouldBeString();
    $info['request']['host']->shouldBeString();
    $info['request']['port']->shouldBeNull();
    $info['request']['path']->shouldBeString();
  }

  function it_returns_an_array_with_statistics() {
    $action = $this->action( 'index' );
    $action( $this->req, $this->res, [ 'format' => 'html' ] );
    $info = $this->information();
    $info['statistics']->shouldBeArray();
    $info['statistics']['start_time']->shouldBeDouble();
    $info['statistics']['used_time']->shouldMatch( '/\d+ms/' );
    $info['statistics']['start_memory']->shouldBeString();
    $info['statistics']['end_memory']->shouldBeString();
    $info['statistics']['used_memory']->shouldBeString();
  }



}





