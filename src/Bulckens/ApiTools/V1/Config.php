<?php

namespace Bulckens\ApiTools\V1;

use Exception;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;

class Config {

  protected static $root;
  protected static $config;
  protected static $file = 'api_tools.yml';
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

  // Get source
  public static function source( $key ) {
    return self::get( "sources.$key" );
  }

  // Get secret
  public static function secret( $key ) {
    // retreive secret using user defined method
    if ( $method = self::get( 'methods.secret' ) )
      return call_user_func( $method, $key );

    // retreive secret from config
    return self::get( "secrets.$key" );
  }

  // Test existence of secret
  public static function secretExists( $key ) {
    // test an array with keys
    if ( is_array( $key ) ) {
      $secrets = array_map( function( $secret ) { return self::secret( $secret ) }, $key );

      return count( $key ) === count( array_filter( $secret ) );
    }

    // test a single secret
    return !! self::secret( $key );
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
      return self::root( 'config/' . self::$file );

    self::$file = $file;
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