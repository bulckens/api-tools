<?php 

namespace Bulckens\ApiTools\V1;

use Exception;

abstract class Secret {

  // Get secret
  public static function get( $key ) {
    // retreive secret using user defined method
    if ( $method = Config::get( 'methods.secret' ) ) {
      if ( is_callable( $method ) )
        return call_user_func( $method, $key );

      throw new SecretMethodNotCallableException( "Secret method $method not callable" );
    }

    // retreive secret from config
    return Config::get( "secrets.$key" );
  }

  // Test existence
  public static function exists( $key ) {
    // test an array with keys
    if ( is_array( $key ) ) {
      $secrets = array_map( function( $secret ) {
        return self::get( $secret );
      }, $key );

      return count( $key ) === count( array_filter( $secrets ) );
    }

    // test a single secret
    return !! self::get( $key );
  }

}

// Exceptions
class SecretMethodNotCallableException extends Exception {}