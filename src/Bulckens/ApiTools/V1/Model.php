<?php 

namespace Bulckens\ApiTools\V1;

use Exception;

abstract class Model {

  protected $uri   = '';
  protected $data  = [];
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

  // Add get parameter
  public function query( $key, $value = null ) {
    if ( is_array( $key ) )
      foreach ( $key as $k => $v ) $this->query[$k] = $v;
    else
      $this->query[$key] = $value;

    return $this;
  }

  // Add post data
  public function data( $key, $value = null ) {
    if ( is_array( $key ) )
      foreach ( $key as $k => $v ) $this->data[$k] = $v;
    else
      $this->data[$key] = $value;

    return $this;
  }

  // Build uri
  public function uri( $format = 'json' ) {
    $uri = "{$this->path()}.$format";

    // add token
    $this->query['token'] = Auth::token( $uri );

    return "$uri?" . http_build_query( $this->query );
  }

  // Get server with optional path
  public function source( $path = '' ) {
    if ( $source = Source::get( $this->key() ) ) {
      $source = preg_replace( '/\/$/', '', $source );

      return "$source$path";
    } else {
      throw new MissingServerException( 'API source not defined' );
    }
  }

  // Get path from uri
  public function path() {
    return explode( ':', $this->uri )[1];
  }

  // Get key from uri
  public function key() {
    return explode( ':', $this->uri )[0];
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
    return call_user_func(
      "Requests::$method"
    , $this->source( $this->uri( $format ) )
    , [ 'Accept' => Mime::type( $format ) ]
    , $this->data
    );
  }

}

// Exceptions
class MissingServerException extends Exception {}