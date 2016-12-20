<?php 

namespace Bulckens\ApiTools\V1;

abstract class Mime {

  protected static $map  = [
    'css'  => 'text/css'
  , 'dump' => 'text/plain'
  , 'js'   => 'application/javascript'
  , 'json' => 'application/json'
  , 'html' => 'text/html'
  , 'txt'  => 'text/plain'
  , 'xml'  => 'application/xml'
  , 'yaml' => 'application/x-yaml'
  ];

  // Get mime output map
  public static function type( $key = null ) {
    if ( is_null( $key ) )
      return self::$map;

    if ( isset( self::$map[$key] ) )
      return self::$map[$key];
  }

}