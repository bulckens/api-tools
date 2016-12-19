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
    if ( file_exists( $file ) ) {
      $config = Yaml::parse( file_get_contents( $file ) );

      // get environment
      if ( isset( $config['generic']['methods']['env'] ) )
        $env = call_user_func( $config['generic']['methods']['env'] );
      else
        throw new MissingEnvMethodException( 'Environment method not defined' );

      // get environment config
      if ( isset( $config[$env] ) )
        self::$config = $config[$env];
      else
        throw new MissingEnvConfigException( 'Environment config not defined' );
    }
    
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

    // prepare path iteration
    $parts = explode( '.', $key );
    $value = self::$config;

    // find value for path
    foreach ( $parts as $part ) {
      if ( isset( $value[$part] ) )
        $value = $value[$part];
      else return null;
    }
    
    return $value;
  }

  // Test existance of config
  public static function exists() {
    return !! self::$config;
  }

  // Get mime output map
  public static function mime( $key = null ) {
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

// Exceptions
class MissingEnvMethodException extends Exception {}
class MissingEnvConfigException extends Exception {}