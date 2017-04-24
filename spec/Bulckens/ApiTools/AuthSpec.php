<?php

namespace spec\Bulckens\ApiTools;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;
use Bulckens\AppTools\App;
use Bulckens\ApiTools\Api;
use Bulckens\ApiTools\Secret;
use Bulckens\ApiTools\Auth;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AuthSpec extends ObjectBehavior {

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


  // Verify method
  function it_verifies_the_validity_of_a_string_token_in_a_request() {
    // $this->beConstructedWith([ 'secret' => 'generic' ]);
    // $this
    //   ->__invoke( $this->req, $this->res, function() { return function() { return 'result'; }; })
    //   ->__toString()
    //   ->shouldStartWith( 'lala' );
  }
  
}
