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

  // Add resource
  public function resource( $resource, $id = null ) {
    return new Resource( $this->add( $resource, $id ) );
  }

  // Add level
  public function add( $part, $id = null ) {
    return implode( '/', array_filter( [ $this->uri, $part, $id ] ) );
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

  // Perform GET request
  public function get( $format = 'json' ) {
    return $this->perform( 'get', $format );
  }

  // Perform POST request
  public function post( $format = 'json' ) {
    return $this->perform( 'post', $format );
  }

  // Perform PUT request
  public function put( $format = 'json' ) {
    return $this->perform( 'put', $format );
  }

  // Perform DELETE request
  public function delete( $format = 'json' ) {
    return $this->perform( 'delete', $format );
  }

  // Perform request
  public function perform( $method, $format = 'json' ) {
    return call_user_func( "Requests::$method", [
      $this->server( $this->uri( $format ) )
    , [ 'Accept' => Config::mime( $format ) ]
    ]);
  }

}

// Exceptions
class MissingServerException extends Exception {}