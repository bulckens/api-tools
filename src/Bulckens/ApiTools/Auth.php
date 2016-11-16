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
    $stamp = $req->getParam( 'stamp' );

    // calculate age of token
    $time = self::stamp();
    $age  = $time - $stamp;
    
    // build verification
    $verification = self::token( $stamp, $uri );

    // verify age of token
    if ( $age > $this->lifespan )
      $output->add([ 'message' => 'Token has expired!' ])
             ->status( 403 );

    if ( $stamp > $time )
      $output->add([ 'message' => 'Timestamp can not be from the future!' ])
             ->status( 403 );

    // verify token
    if ( $token != $verification )
      $output->add([ 'message' => 'Invalid token!' ])
             ->status( 401 );

    // passes
    if ( $output->ok() )
      return $next( $req, $res );

    // error
    return $res->withHeader( 'Content-type', $output->mime() )
               ->withStatus( $output->status() )
               ->write( $output->render() );
  }

  // Build authentication token
  public static function token( $stamp, $uri ) {
    return md5( implode( '---', [ Config::get( 'secret' ), $stamp, $uri ] ) );
  }

  // Get current timestamp
  public static function stamp() {
    $date = new DateTime();
    $date->setTimezone( new DateTimeZone( 'GMT' ) );
    return $date->getTimestamp();
  }

}