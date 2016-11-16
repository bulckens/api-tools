<?php

namespace Bulckens\ApiTools;

use Exception;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;

class Config {

  protected static $root;
  protected static $config;

  // Load configuration
  public function __construct() {
    // get config file name
    $file = self::root( 'config/api_auth.yml' );

    // load config
    if ( file_exists( $file ) )
      self::$config = Yaml::parse( file_get_contents( $file ) );

    // fail if none found
    else
      throw new MissingConfigException( "Missing config in $file" );
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

// Exceptions
class MissingConfigException extends Exception {}