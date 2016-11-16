<?php

namespace Bulckens\ApiTools;

class Config {

  protected static $root;
  protected static $config;

  // Load configuration
  public function __construct() {
    self::$config = Yaml::parse( file_get_contents( self::root( 'config/api_auth.yml' ) ) );
  }

  // Get key/value
  public static function get( $key ) {
    if ( isset( self::$config[$key] ) )
      return self::$config[$key];
  }

  // Get host project root
  public static function root( $path = '' ) {
    if ( ! self::$root ) {
      $dir = __DIR__;
      
      while ( ! preg_match( '/\/vendor$/', $dir ) )
        $dir = dirname( $dir );

      self::$root = dirname( $dir ); 
    }

    return Str::finish( self::$root, '/' ) . $path;
  }

}