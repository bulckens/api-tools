<?php

namespace Bulckens\ApiTools\V1;

use Exception;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;

class Config {

  protected static $map;
  protected static $root;
  protected static $config;

  // Load configuration
  public function __construct() {
    // get config file name
    $file = self::root( 'config/api_tools.yml' );
    
    // load config
    if ( file_exists( $file ) )
      self::$config = Yaml::parse( file_get_contents( $file ) );

    // define mime map
    self::$map = [
      'json' => 'application/json'
    , 'yaml' => 'application/x-yaml'
    , 'xml'  => 'application/xml'
    , 'dump' => 'text/plain'
    ];
  }

  // Get key/value
  public static function get( $key ) {
    if ( empty( self::$config ) )
      new self();

    if ( isset( self::$config[$key] ) )
      return self::$config[$key];
  }

  // Test existance of config
  public static function exists() {
    return !! self::$config;
  }

  // Get output map
  public static function map( $key = null ) {
    if ( is_null( $key ) )
      return self::$map;

    if ( isset( self::$map[$key] ) )
      return self::$map[$key];
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