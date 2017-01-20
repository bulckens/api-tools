<?php 

namespace Bulckens\ApiTools\V1;

use Exception;

abstract class Model {

  protected $uri   = ':';
  protected $data  = [];
  protected $query = [];

  // Add resource
  public function resource( $resource, $id = null ) {
    // prepare prefix
    $prefix = empty( $this->part( 'path' ) ) ? '' : '/';

    // add new resource
    $this->uri .= $prefix . implode( '/', array_filter( [ $resource, $id ] ) );

    return $this;
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

  // Build path with format
  public function path( $format = 'json' ) {
    return "/{$this->part( 'path' )}.$format";
  }

  // Get server with optional path
  public function source( $source = null ) {
    if ( is_null( $source ) ) {
      // act as getter
      if ( $source = Source::get( $this->part( 'source' ) ) )
        return preg_replace( '/\/$/', '', $source );
      else
        throw new MissingServerException( 'API source not defined' );
    }

    // act as setter
    $this->uri = "$source:{$this->parts(1)}";

    return $this;
  }

  // Get full uri; path with get params
  public function uri( $format = 'json' ) {
    $path = $this->path( $format );

    // add token
    $this->query['token'] = Auth::token( $path );

    return "$path?" . http_build_query( $this->query );
  }

  // Get full url; server with path
  public function url( $format = 'json', $ssl = null ) {
    $source = $this->source();

    // force https
    if ( $ssl === true )
      $source = preg_replace( '/^http:/', 'https:', $source );

    // force http
    if ( $ssl === false )
      $source = preg_replace( '/^https:/', 'http:', $source );

    return $source . $this->uri( $format );
  }

  // Get part from uri
  public function part( $key ) {
    return $this->parts( $key == 'source' ? 0 : 1 );
  }

  // Get uri parts
  public function parts( $index = null ) {
    $parts = explode( ':', $this->uri );

    if ( is_null( $index ) )
      return $parts;
    else
      return $this->parts()[$index];
  }

  // Perform GET request
  public function get( $format = 'json', $ssl = null ) {
    return $this->perform( 'get', $format );
  }

  // Perform POST request
  public function post( $format = 'json', $ssl = null ) {
    return $this->perform( 'post', $format );
  }

  // Perform PUT request
  public function put( $format = 'json', $ssl = null ) {
    return $this->perform( 'put', $format );
  }

  // Perform DELETE request
  public function delete( $format = 'json', $ssl = null ) {
    return $this->perform( 'delete', $format );
  }

  // Perform request
  protected function perform( $method, $format = 'json', $ssl = null ) {
    // build new response
    $response = new Response( $format );

    // perform request
    $request = call_user_func(
      "Requests::$method"
    , $this->url( $format, $ssl )
    , [ 'Accept' => Mime::type( $format ) ]
    , $this->data
    );

    return $response->body( $request->body );
  }

}

// Exceptions
class MissingServerException extends Exception {}