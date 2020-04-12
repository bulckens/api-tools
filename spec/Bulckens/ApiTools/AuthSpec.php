<?php

namespace spec\Bulckens\ApiTools;

use Slim\Route;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;
use Bulckens\Helpers\TimeHelper;
use Bulckens\AppTools\App;
use Bulckens\ApiTools\Api;
use Bulckens\ApiTools\Secret;
use Bulckens\ApiTools\Token;
use Bulckens\ApiTools\Auth;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AuthSpec extends ObjectBehavior {

  protected $req;
  protected $res;

  function let() {
    $app = new App( 'dev', __DIR__, 3 );
    $app->module( 'api', new Api() );
    $app->run();

    $environment = Environment::mock([
      'REQUEST_URI' => '/mastaba/fake.json'
    ]);
    $this->req = Request::createFromEnvironment( $environment );
    $route = new Route( ['GET'], '/{entity}/fake.json', function() {});
    $this->req = $this->req->withAttribute( 'route', $route );
    $this->res = new Response( 200 );

    $this->beConstructedWith([ 'secret' => 'generic' ]);
  }

  function letGo() {
    Api::get()->configFile( 'api.yml' );
  }


  // Invoke method
  function it_returns_a_response_object() {
    $token = new Token( '/mastaba/fake.json', 'reverse' );
    $this->beConstructedWith([ 'secret' => 'fladdr' ]);
    
    $req = $this->req->withQueryParams([ 'token' => $token->get() ]);
    $this
      ->__invoke( $req, $this->res, function() {})
      ->shouldHaveType( 'Slim\Http\Response' );
  }

  function it_indicates_a_missing_secret() {
    $token = new Token( '/mastaba/fake.json', 'reverse' );
    $this->beConstructedWith([ 'secret' => 'fladdr' ]);
    
    $req = $this->req->withQueryParams([ 'token' => $token->get() ]);
    $this->__invoke( $req, $this->res, function() {});
    $this->output()->toArray()->shouldHaveKeyWithValue( 'error', 'secret.missing' );
  }

  function it_indicates_a_secret_missing_from_the_uri() {
    Api::get()->configFile( 'api.secret.yml' );

    $token = new Token( '/mastaba/fake.json', 'generic' );
    $this->beConstructedWith([ 'secret' => 'uri.doctor_who' ]);

    $req = $this->req->withQueryParams([ 'token' => $token->get() ]);
    $this->__invoke( $req, $this->res, function() {});
    $this->output()->toArray()->shouldHaveKeyWithValue( 'error', 'secret.missing_from_uri' );
  }

  function it_indicates_a_missing_token() {
    $this->beConstructedWith([ 'secret' => 'generic' ]);

    $this->__invoke( $this->req, $this->res, function() {});
    $this->output()->toArray()->shouldHaveKeyWithValue( 'error', 'token.missing' );
  }

  function it_indicates_failed_token_verification() {
    $token = new Token( '/mastaba/fake.json', 'generic' );
    $this->beConstructedWith([ 'secret' => 'reverse' ]);

    $req = $this->req->withQueryParams([ 'token' => $token->get() ]);
    $this->__invoke( $req, $this->res, function() {});
    $this->output()->toArray()->shouldHaveKeyWithValue( 'error', 'token.invalid' );
  }

  function it_indicates_an_expired_token() {
    $token = new Token( '/mastaba/fake.json', 'generic', TimeHelper::ms() - 7200000 );
    $this->beConstructedWith([ 'secret' => 'generic' ]);
    
    $req = $this->req->withQueryParams([ 'token' => $token->get() ]);
    $this->__invoke( $req, $this->res, function() {});
    $this->output()->toArray()->shouldHaveKeyWithValue( 'error', 'token.expired' );
  }

  function it_indicates_a_futuristic_token() {
    $token = new Token( '/mastaba/fake.json', 'generic', TimeHelper::ms() + 7200000 );
    $this->beConstructedWith([ 'secret' => 'generic' ]);
    
    $req = $this->req->withQueryParams([ 'token' => $token->get() ]);
    $this->__invoke( $req, $this->res, function() {});
    $this->output()->toArray()->shouldHaveKeyWithValue( 'error', 'token.futuristic' );
  }

  function it_accepts_a_token_bearer() {
    $token = (new Token( '/mastaba/fake.json', 'generic' ))->get();
    $this->beConstructedWith([ 'secret' => 'generic' ]);
    
    $req = $this->req->withHeader( 'Authorization', "Bearer $token" );
    $this->__invoke( $req, $this->res, function() {});
    $this->output()->toArray()->shouldNotHaveKeyWithValue( 'error', 'token.missing' );
    $this->output()->toArray()->shouldNotHaveKey( 'error' );
  }


  // Validate method
  function it_verifies_the_validity_of_a_string_secret_in_a_request() {
    $token = new Token( '/mastaba/fake.json', 'generic' );
    $this
      ->validate( $this->req->withQueryParams([ 'token' => $token->get() ]) )
      ->shouldBe( true );
  }

  function it_verifies_the_validity_of_an_array_of_secrets_in_a_request() {
    $this->beConstructedWith([ 'secret' => [ 'generic', 'reverse' ] ]);
    $token = new Token( '/mastaba/fake.json', 'reverse' );
    $this
      ->validate( $this->req->withQueryParams([ 'token' => $token->get() ]) )
      ->shouldBe( true );
  }

  function it_verifies_the_validity_of_a_secret_in_a_request_using_a_dynamic_method() {
    Api::get()->configFile( 'api.secret.yml' );

    $this->beConstructedWith([ 'secret' => 'generic' ]);
    $token = new Token( '/mastaba/fake.json', 'generic' );
    $this
      ->validate( $this->req->withQueryParams([ 'token' => $token->get() ]) )
      ->shouldBe( true );
  }

  function it_verifies_the_validity_of_a_uri_secret_in_a_request() {
    Api::get()->configFile( 'api.secret.yml' );

    $this->req->getAttribute( 'route' )->setArgument( 'entity', 'mastaba' );

    $this->beConstructedWith([ 'secret' => 'uri.entity' ]);
    $token = new Token( '/mastaba/fake.json', 'mastaba' );

    $this
      ->validate( $this->req->withQueryParams([ 'token' => $token->get() ]) )
      ->shouldBe( true );
  }

  function it_fails_if_the_secret_key_is_missing() {
    $token = new Token( '/mastaba/fake.json', 'reverse' );
    $this->beConstructedWith([ 'secret' => 'fladdr' ]);
    $this
      ->shouldThrow( 'Bulckens\ApiTools\AuthMissingSecretException' )
      ->duringValidate( $this->req->withQueryParams([ 'token' => $token->get() ]) );
  }

  function it_fails_if_all_secret_keys_are_missing() {
    $token = new Token( '/mastaba/fake.json', 'reverse' );
    $this->beConstructedWith([ 'secret' => [ 'fladdr', 'falimbu' ] ]);
    $this
      ->shouldThrow( 'Bulckens\ApiTools\AuthMissingSecretException' )
      ->duringValidate( $this->req->withQueryParams([ 'token' => $token->get() ]) );
  }

  function it_fails_if_the_secret_key_could_not_be_found_dynamically() {
    Api::get()->configFile( 'api.secret.yml' );

    $token = new Token( '/mastaba/fake.json', 'reverse' );
    $this->beConstructedWith([ 'secret' => 'dynamo' ]);
    $this
      ->shouldThrow( 'Bulckens\ApiTools\AuthMissingSecretException' )
      ->duringValidate( $this->req->withQueryParams([ 'token' => $token->get() ]) );
  }

  function it_fails_if_the_uri_secret_is_missing_from_the_uri() {
    Api::get()->configFile( 'api.secret.yml' );

    $token = new Token( '/mastaba/fake.json', 'generic' );
    $this->beConstructedWith([ 'secret' => 'uri.doctor_who' ]);
    $this
      ->shouldThrow( 'Bulckens\ApiTools\AuthSecretNotDefinedInUriException' )
      ->duringValidate( $this->req->withQueryParams([ 'token' => $token->get() ]) );
  }

  function it_fails_if_no_secrets_are_given() {
    $token = new Token( '/mastaba/fake.json', 'reverse' );
    $this->beConstructedWith([ 'secret' => [] ]);
    $this
      ->shouldThrow( 'Bulckens\ApiTools\AuthMissingSecretException' )
      ->duringValidate( $this->req->withQueryParams([ 'token' => $token->get() ]) );
  }

  function it_fails_if_no_token_is_given() {
    $this->beConstructedWith([ 'secret' => 'generic' ]);
    $this
      ->shouldThrow( 'Bulckens\ApiTools\AuthMissingTokenException' )
      ->duringValidate( $this->req );
  }

  function it_fails_if_token_can_not_be_verified() {
    $token = new Token( '/mastaba/fake.json', 'generic' );
    $this->beConstructedWith([ 'secret' => 'reverse' ]);
    $this
      ->shouldThrow( 'Bulckens\ApiTools\AuthTokenVerificationFailedException' )
      ->duringValidate( $this->req->withQueryParams([ 'token' => $token->get() ]) );
  }

  function it_fails_if_token_is_expired() {
    $token = new Token( '/mastaba/fake.json', 'generic', TimeHelper::ms() - 7200000 );
    $this->beConstructedWith([ 'secret' => 'generic' ]);
    $this
      ->shouldThrow( 'Bulckens\ApiTools\AuthTokenExpiredException' )
      ->duringValidate( $this->req->withQueryParams([ 'token' => $token->get() ]) );
  }

  function it_fails_if_token_is_from_the_future() {
    $token = new Token( '/mastaba/fake.json', 'generic', TimeHelper::ms() + 7200000 );
    $this->beConstructedWith([ 'secret' => 'generic' ]);
    $this
      ->shouldThrow( 'Bulckens\ApiTools\AuthTokenFuturisticException' )
      ->duringValidate( $this->req->withQueryParams([ 'token' => $token->get() ]) );
  }


  // Output method
  function it_returns_the_output_object() {
    $token = new Token( '/mastaba/fake.json', 'generic' );
    $this->validate( $this->req->withQueryParams([ 'token' => $token->get() ]) );
    $this->output()->shouldHaveType( 'Bulckens\AppTools\Output' );
  }
  
}
