<?php

namespace Bulckens\ApiTools\V1;

use Exception;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;

class Config {

  protected static $map;
  protected static $root;
  protected static $config;
  protected static $file = 'api_tools.yml';

  // Load configuration
  public function __construct() {
    // load config
    if ( file_exists( self::file() ) ) {
      $config = Yaml::parse( file_get_contents( self::file() ) );

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

  // Get secret
  public static function secret( $key ) {
    if ( $method = self::get( 'methods.secret' ) )
      return call_user_func( $method, $key );

    return self::get( "secrets.$key" );
  }

  // Get source
  public static function source( $key ) {
    return self::get( "sources.$key" );
  }

  // Test existence of config
  public static function exists() {
    return file_exists( self::file() );
  }

  // Get mime output map
  public static function mime( $key = null ) {
    if ( is_null( $key ) )
      return self::$map;

    if ( isset( self::$map[$key] ) )
      return self::$map[$key];
  }

  // Get config file path
  public static function file( $file = null ) {
    if ( is_null( $file ) )
      self::$file = $file;

    return self::root( "config/{self::$file}" );
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