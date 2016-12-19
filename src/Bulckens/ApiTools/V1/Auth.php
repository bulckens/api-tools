<?php

namespace Bulckens\ApiTools\V1;

use Exception;

class Auth {

  protected $lifespan;
  protected $secret;

  // Initialize api middleware
  public function __construct( $options = [] ) {
    $defaults = [ 'lifespan' => 30, 'secret' => 'generic' ];
    $options  = array_replace( $defaults, $options );

    $this->lifespan = $options['lifespan'] * 1000;
    $this->secret   = $options['secret'];
  }

  // Magic middleware method
  public function __invoke( $req, $res, $next ) {
    // get current uri and format
    $uri    = $req->getUri()->getPath();
    $format = pathinfo( $uri, PATHINFO_EXTENSION );

    // initialize output container
    $output = new Output( $format );

    // get token and timestamp
    $token = $req->getParam( 'token' );
    $stamp = hexdec( substr( $token, 64 ) );

    // calculate age of token
    $time = self::stamp();
    $age  = $time - $stamp;

    // get token key from uri value
    if ( preg_match( '/^uri\.([a-z0-9\_\-]+)/', $this->secret, $match ) ) {
      $route = $req->getAttribute( 'route' );
      $param = $route->getArgument( $match[1] );

      $this->secret = $param;
    }

    // Get token lifespan from config, if defined
    if ( $lifespan = Config::get( 'lifespan' ) )
      $this->lifespan = $lifespan * 1000;
    
    // build verification
    $verification = self::token( $uri, $stamp, $this->secret );

    // verify existance of local config file
    if ( ! Config::exists() || ! Config::get( "secrets.$this->secret" ) )
      $output->add([ 'error' => 'secret.missing' ])
             ->status( 500 );

    // verify token
    else if ( empty( $token ) || $token != $verification )
      $output->add([ 'error' => 'token.invalid' ])
             ->status( 401 );

    // verify of token is not too old
    else if ( $age > $this->lifespan )
      $output->add([ 'error' => 'token.expired' ])
             ->status( 403 );

    // verify if token is not too young (with a 5 second buffer to make up for minor differences)
    else if ( $stamp > $time + 5 )
      $output->add([ 'error' => 'token.futuristic' ])
             ->status( 403 );

    // passes
    else if ( $output->ok() )
      return $next( $req, $res );

    // error
    return $res->withHeader( 'Content-type', $output->mime() )
               ->withStatus( $output->status() )
               ->write( $output->render() );
  }

  // Build authentication token
  public static function token( $uri, $stamp = null, $secret = 'generic' ) {
    if ( $secret = Config::get( "secrets.$secret" ) ) {
      $stamp = $stamp ?: self::stamp();
      return hash( 'sha256', implode( '---', [ $secret, $stamp, $uri ] ) ) . dechex( $stamp );

    } else {
      throw new MissingSecretException( 'Missing API secret!' );
    }
  }

  // Get current timestamp
  public static function stamp() {
    return round( microtime( 1 ) * 1000 );
  }

}

// Exceptions
class MissingSecretException extends Exception {}
