<?php 

namespace Bulckens\ApiTools\V1;

abstract class Mime {

  protected static $map  = [
    'css'  => 'text/css'
  , 'dump' => 'text/plain'
  , 'js'   => 'application/javascript'
  , 'json' => 'application/json'
  , 'txt'  => 'text/plain'
  , 'xml'  => 'application/xml'
  , 'yaml' => 'application/x-yaml'
  ];

  // Get mime output map
  public static function type( $format = null ) {
    if ( is_null( $format ) )
      return self::$map;

    if ( isset( self::$map[$format] ) )
      return self::$map[$format];
  }

  // Comment text string based on format
  public static function comment( $format, $string ) {
    switch ( $format ) {
      case 'js':
      case 'css':
        return "/*\n$string\n*/";
      break;
      default:
        return $string;
      break;
    }
  }

}