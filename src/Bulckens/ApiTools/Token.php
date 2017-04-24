<?php

namespace Bulckens\ApiTools;

use Exception;
use Bulckens\Helpers\TimeHelper;

class Token {
  
  protected $uri;
  protected $stamp;
  protected $stampless;
  protected $secret;

  public function __construct( $uri, $secret, $stamp = null ) {
    // store uri
    $this->uri = $uri;

    // store or generate timestamp
    $this->stampless = is_null( $stamp );
    $this->stamp = $stamp ?: TimeHelper::ms();
    
    // find secret
    $this->secret = Secret::get( $secret );

    // fail if no secret could be found
    if ( ! $this->secret )
      throw new TokenSecretMissingException( "Secret could not be found for $secret" ); 
  }


  // Get generated token
  public function get() {
    $parts = [ $this->secret, $this->stamp, $this->uri ];
    return hash( 'sha256', implode( '---', $parts ) ) . dechex( $this->stamp );
  }
  

  // Validate a token agains the current given parameters
  public function validate( $token ) {
    // use the token's stamp if none was explicitly given
    if ( $this->stampless )
      $this->stamp = self::timestamp( $token );

    return $token === $this->get();
  }


  // URI getter
  public function uri() {
    return $this->uri;
  }


  // Return converted secret
  public function secret() {
    return $this->secret;
  }


  // Given or generated timestamp
  public function stamp() {
    return $this->stamp;
  }


  // Parse timestamp from given token
  public static function timestamp( $token ) {
    return hexdec( substr( $token, 64 ) );
  }

}


// Exceptions
class TokenSecretMissingException extends Exception {}
