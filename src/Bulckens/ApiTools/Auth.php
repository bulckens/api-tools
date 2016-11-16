<?php

namespace Bulckens\ApiTools;

use DateTime;
use DateTimeZone;
use Illuminate\Support\Str;

class Auth {

  protected $lifespan;

  // Initialize api middleware
  public function __construct( $lifespan = 30 ) {
    $this->lifespan = $lifespan;
  }

  // Magic middleware method
  public function __invoke( $req, $res, $next ) {
    // get token and timestamp
    $params = $req->getQueryParams();
    $token  = $params['token'];
    $stamp  = $params['stamp'] * 1;

    // get current uri and format
    $uri = $req->getUri()->getPath()

    // calculate age of token
    $time = self::stamp();
    $age  = $time - $stamp;
    
    // verify age of token
    if ( $age > $this->lifespan )
      return $this->error( $res, 'Token has expired!', 403 );
    else if ( $stamp > $time )
      return $this->error( $res, 'Timestamp can not be from the future!', 403 );

    // build verification
    $verification = self::token( $stamp, $uri );
    
    // verify token
    if ( $token != $verification )
      return $this->error( $res, 'Invalid token!' );

    return $next( $req, $res );
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

  // Render an error
  protected function error( $res, $message, $code = 401 ) {
    return $res->withStatus( $code )
               ->withHeader( 'Content-type', Output::mime( $this->format ) )
               ->write( Output::error( $message, $this->format, $code ) );
  }

}