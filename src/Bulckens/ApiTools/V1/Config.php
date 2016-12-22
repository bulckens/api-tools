<?php

namespace Bulckens\ApiTools\V1;

use Exception;
use Symfony\Component\Yaml\Yaml;

class Config {

  protected static $env;
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
        self::$env = call_user_func( $config['generic']['methods']['env'] );
      else
        throw new MissingEnvMethodException( 'Environment method not defined' );

      // get environment config
      if ( isset( $config[self::$env] ) )
        self::$config = $config[self::$env];
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

  // Test existence of config
  public static function exists() {
    return file_exists( self::file() );
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
      
      // find vendor dir
      while ( ! preg_match( '/\/vendor$/', $dir ) )
        $dir = dirname( $dir );

      // detect capistrano installation
      if ( basename( dirname( $dir ) ) == 'shared' )
        self::$root = dirname( dirname( $dir ) ) . '/current';
      else
        self::$root = dirname( $dir ); 
    }

    return str_replace( '//', '/', self::$root . "/$path" );
  }

  // Test current environment
  public static function env( $test = null ) {
    if ( is_null( $test ) )
      return self::$env;

    return $test == self::$env;
  }

}

// Exceptions
class MissingEnvMethodException extends Exception {}
class MissingEnvConfigException extends Exception {}