<?php

namespace Bulckens\ApiTools;

use DateTime;
use DateTimeZone;

class Auth {

  protected $lifespan;

  // Initialize api middleware
  public function __construct( $lifespan = 30 ) {
    $this->lifespan = $lifespan;

    // initialize new config
    new Config();
  }

  // Magic middleware method
  public function __invoke( $req, $res, $next ) {
    // get current uri and format
    $uri    = $req->getUri()->getPath();
    $format = pathinfo( $uri, PATHINFO_EXTENSION );

    // initialize output container
    $output = new Output( $format, [ 'root' => 'error' ]);

    // get token and timestamp
    $token = $req->getParam( 'token' );
    $stamp = hexdec( substr( $token, 32 ) );

    // calculate age of token
    $time = self::stamp();
    $age  = $time - $stamp;
    
    // build verification
    $verification = self::token( $stamp, $uri );

    // verify existance of local config file
    if ( ! Config::exists() )
      $output->add([ 'message' => 'Missing secret!' ])
             ->status( 500 );

    // verify token
    else if ( empty( $token ) || $token != $verification )
      $output->add([ 'message' => 'Invalid token!' ])
             ->status( 401 );

    // verify of token is not too old
    else if ( $age > $this->lifespan )
      $output->add([ 'message' => 'Token has expired!' ])
             ->status( 403 );

    // verify if token is not too young (with a 5 second buffer to make up for minor differences)
    else if ( $stamp > $time + 5 )
      $output->add([ 'message' => 'Token can not be from the future!' ])
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
  public static function token( $stamp, $uri ) {
    return hash( 'sha256', implode( '---', [ Config::get( 'secret' ), $stamp, $uri ] ) ) . dechex( $stamp );
  }

  // Get current timestamp
  public static function stamp() {
    $date = new DateTime();
    $date->setTimezone( new DateTimeZone( 'GMT' ) );
    return $date->getTimestamp();
  }

}