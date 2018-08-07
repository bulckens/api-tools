<?php 

namespace Bulckens\ApiTools;

use Exception;

abstract class Source {

  // Get source
  public static function get( $key ) {
    // retreive source using user defined method
    if ( $method = Api::get()->config( 'methods.source' ) ) {
      if ( ! is_callable( $method ) )
        throw new SourceMethodNotCallableException( "Source method $method not callable" );
      
      if ( $secret = call_user_func( $method, $key ) )
        return $secret;
    }

    return Api::get()->config( "sources.$key" );
  }

}

// Exceptions
class SourceMethodNotCallableException extends Exception {}