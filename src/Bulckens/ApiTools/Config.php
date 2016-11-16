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
    $file = self::root( 'config/api_tools.yml' );

    // load config
    if ( file_exists( $file ) )
      self::$config = Yaml::parse( file_get_contents( $file ) );
  }

  // Get key/value
  public static function get( $key ) {
    if ( isset( self::$config[$key] ) )
      return self::$config[$key];
  }

  // Test existance of config
  public static function exists() {
    return !! self::$config;
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