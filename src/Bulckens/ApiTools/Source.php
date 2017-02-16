<?php 

namespace Bulckens\ApiTools;

abstract class Source {

  // Get source
  public static function get( $key ) {
    return Api::get()->config( "sources.$key" );
  }

}