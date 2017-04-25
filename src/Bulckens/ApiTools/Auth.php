<?php

namespace Bulckens\ApiTools;

use Exception;
use Bulckens\Helpers\TimeHelper;

class Auth {

  protected $lifespan;
  protected $secret;
  protected $output;

  // Initialize api middleware
  public function __construct( $options = [] ) {
    $defaults = [
      'lifespan' => Api::get()->config( 'lifespan' )
    , 'secret'   => 'generic'
    ];
    $options  = array_replace( $defaults, $options );

    $this->lifespan = $options['lifespan'] * 1000;
    $this->secrets  = is_string( $options['secret'] ) ?
      [ $options['secret'] ] : $options['secret'];
  }


  // Magic middleware method
  public function __invoke( $req, $res, $next ) {
    try {
      // validate request
      $this->validate( $req );

      // move on to the next middleware
      return $next( $req, $res );

    } catch ( AuthMissingSecretException $e ) {
      $this->output->add([
        'error'   => 'secret.missing'
      , 'details' => [ 'requirements' => $this->secrets ]
      ])->status( 403 );

    } catch ( AuthSecretNotDefinedInUriException $e ) {
      $this->output->add([
        'error'   => 'secret.missing_from_uri'
      , 'details' => [ 'requirements' => $this->secrets ]
      ])->status( 403 );
    
    } catch ( AuthMissingTokenException $e ) {
      $this->output->add([
        'error' => 'token.missing'
      ])->status( 403 );

    } catch ( AuthTokenVerificationFailedException $e ) {
      $this->output->add([
        'error' => 'token.invalid'
      ])->status( 401 );

    } catch ( AuthTokenExpiredException $e ) {
      $this->output->add([
        'error' => 'token.expired'
      ])->status( 401 );

    } catch ( AuthTokenFuturisticException $e ) {
      $this->output->add([
        'error' => 'token.futuristic'
      ])->status( 401 );

    }

    // error output
    return $res
      ->withHeader( 'Content-type', $this->output->mime() )
      ->withStatus( $this->output->status() )
      ->write( $this->output->render() );
  }


  // Validate request token from request
  public function validate( $req ) {
    // get current uri and format
    $uri    = $req->getUri()->getPath();
    $format = pathinfo( $uri, PATHINFO_EXTENSION );

    // initialize output container
    $this->output = new Output( $format );

    // get token and timestamp from request
    $token = $req->getParam( 'token' );
    
    if ( empty( $token ) )
      throw new AuthMissingTokenException( 'Expected a token but none is given' );
    
    // calculate age of token
    $stamp = Token::timestamp( $token );
    $time  = TimeHelper::ms();
    $age   = $time - $stamp;

    // collect secrets from uri params
    $secrets = [];

    foreach ( $this->secrets as $key ) {
      if ( preg_match( '/^uri\.([a-z0-9\_\-]+)/', $key, $match ) ) {
        // get secret key name form uri
        if ( $key_name = $req->getAttribute( 'route' )->getArgument( $match[1] ) )
          array_push( $secrets, $key_name );
        else
          throw new AuthSecretNotDefinedInUriException( "Could not find key name for $key in uri" );

      } else {
        array_push( $secrets, $key );
      }
    }

    // test existance of secrets
    if ( empty( $secrets ) || ! Secret::exists( $secrets ) )
      throw new AuthMissingSecretException( 'Could not find secret for ' . implode( ' or ', $secrets ) );

    // build tokens
    $tokens = array_map( function( $secret ) use( $uri, $stamp ) {
      $token = new Token( $uri, $secret, $stamp );
      return $token->get();
    }, $secrets );

    // verify token validity
    if ( ! in_array( $token, $tokens ) )
      throw new AuthTokenVerificationFailedException( 'The provided token is invalid' );
    
    // verify token age
    if ( $age > $this->lifespan )
      throw new AuthTokenExpiredException( 'The provided token is no longer valid' );

    else if ( $stamp > $time + 5 )
      throw new AuthTokenFuturisticException( 'The provided token is from the future' );
      

    // return output status test
    return $this->output->ok();
  }


  // Return output object
  public function output() {
    return $this->output;
  }

}

// Exceptions
class AuthMissingTokenException            extends Exception {}
class AuthMissingSecretException           extends Exception {}
class AuthSecretNotDefinedInUriException   extends Exception {}
class AuthTokenVerificationFailedException extends Exception {}
class AuthTokenExpiredException            extends Exception {}
class AuthTokenFuturisticException         extends Exception {}
