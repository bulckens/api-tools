<?php

namespace Bulckens\ApiTools;

use Exception;

class Auth {

  protected $lifespan;
  protected $secret;

  // Initialize api middleware
  public function __construct( $options = [] ) {
    $defaults = [
      'lifespan' => Api::get()->config( 'lifespan' )
    , 'secret'   => 'generic'
    ];
    $options  = array_replace( $defaults, $options );

    $this->lifespan   = $options['lifespan'] * 1000;
    $this->secret_key = $options['secret'];
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
    if ( is_string( $this->secret_key ) && preg_match( '/^uri\.([a-z0-9\_\-]+)/', $this->secret_key, $match ))
      $this->secret_key = $req->getAttribute( 'route' )->getArgument( $match[1] );

    // verify existance of secret
    if ( Secret::exists( $this->secret_key ) ) {
      // ensure an array of secrets
      $secret_keys = is_array( $this->secret_key ) ? $this->secret_key : [ $this->secret_key ];
      
      // build verifications
      $verifications = array_map( function( $secret_key ) use( $uri, $stamp ) {
        return self::token( $uri, $stamp, $secret_key );
      }, $secret_keys );

      // verify token
      if ( empty( $token ) || ! in_array( $token, $verifications ) )
        $output->add([ 'error' => 'token.invalid' ])->status( 401 );

      // verify of token is not too old
      else if ( $age > $this->lifespan )
        $output->add([ 'error' => 'token.expired' ])->status( 403 );

      // verify if token is not too young (with a 5 second buffer to make up for minor differences)
      else if ( $stamp > $time + 5 )
        $output->add([ 'error' => 'token.futuristic' ])->status( 403 );

      // passes
      else if ( $output->ok() )
        return $next( $req, $res );

    } else {
      $output->add([
        'error'   => 'secret.missing'
      , 'details' => [ 'secret' => $this->secret_key ]
      ])->status( 403 );
    }

    // error
    return $res->withHeader( 'Content-type', $output->mime() )
               ->withStatus( $output->status() )
               ->write( $output->render() );
  }

}

// Exceptions
class AuthMissingSecretException extends Exception {}
