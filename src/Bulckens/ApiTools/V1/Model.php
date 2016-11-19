<?php 

namespace Bulckens\ApiTools\V1;

use Exception;

abstract class Model {

  protected $uri   = '';
  protected $query = [];

  // Register new uri part
  public function register( $part ) {
    $this->uri .= $part;
  }

  // Perform request
  public function get( $format = 'json' ) {
    return file_get_contents( $this->server( $this->uri( $format ) ) );
  }

  // Add level
  public function add( $part ) {
    return "$this->uri/$part";
  }

  // Add parameter
  public function query( $key, $value ) {
    $this->query[$key] = $value;

    return $this;
  }

  // Get uri
  public function uri( $format = 'json' ) {
    // build uri
    $uri = "{$this->uri}.$format";
    
    // add token
    $this->query['token'] = Auth::token( $uri );

    return "$uri?" . http_build_query( $this->query );
  }

  // Get server with optional path
  public function server( $path = '' ) {
    if ( $server = Config::get( 'server' ) ) {
      $server = preg_replace( '/\/$/', '', $server );

      return "$server$path";
    } else {
      throw new MissingServerException( 'API server not defined' );
    }
  }

  // Get resources as XML
  public function xml() {
    return $this->get( 'xml' );
  }

  // Get resources as JSON
  public function json() {
    return $this->get( 'json' );
  }

  // Get resources as YAML
  public function yaml() {
    return $this->get( 'yaml' );
  }

  // Get resources as PHP dump
  public function dump() {
    return $this->get( 'dump' );
  }

}

// Exceptions
class MissingServerException extends Exception {}