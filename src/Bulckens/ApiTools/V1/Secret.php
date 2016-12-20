<?php 

namespace Bulckens\ApiTools\V1;

abstract class Secret {

  // Get secret
  public static function get( $key ) {
    // retreive secret using user defined method
    if ( $method = Config::get( 'methods.secret' ) )
      return call_user_func( $method, $key );

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