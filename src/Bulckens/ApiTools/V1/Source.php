<?php 

namespace Bulckens\ApiTools\V1;

abstract class Source {

  // Get source
  public static function get( $key ) {
    return Config::get( "sources.$key" );
  }

}