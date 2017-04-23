<?php 

namespace Bulckens\ApiTools;

use Exception;

abstract class Secret {

  // Get secret
  public static function get( $key ) {
    // retreive secret using user defined method
    if ( $method = Api::get()->config( 'methods.secret' ) ) {
      if ( ! is_callable( $method ) )
        throw new SecretMethodNotCallableException( "Secret method $method not callable" );
      
      if ( $secret = call_user_func( $method, $key ) )
        return $secret;
    }

    // retreive secret from config
    return Api::get()->config( "secrets.$key" );
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