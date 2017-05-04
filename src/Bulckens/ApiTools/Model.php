<?php 

namespace Bulckens\ApiTools;

use Exception;
use Bulckens\AppTools\Mime;

abstract class Model {

  protected $data  = [];
  protected $query = [];
  protected $secret;
  protected $source;
  protected $path;

  // Add resource
  public function resource( $resource, $id = null ) {
    // prepare prefix
    $prefix = empty( $this->path ) ? '' : '/';

    // add new resource
    $this->path .= $prefix . implode( '/', array_filter( [ $resource, $id ] ) );

    return $this;
  }


  // Add get parameter
  public function query( $key = null, $value = null ) {
    if ( is_null( $key ) )
      return $this->query;
    else if ( is_array( $key ) )
      foreach ( $key as $k => $v ) $this->query[$k] = $v;
    else
      $this->query[$key] = $value;

    return $this;
  }
  

  // Add order values to query
  public function order( $key, $value = null ) {
    if ( ! $key instanceof Sort )
      $key = new Sort( $key, $value );

    return $this->query( 'order', $key->get() );
  }
  

  // Add post data
  public function data( $key = null, $value = null ) {
    if ( is_null( $key ) )
      return $this->data;
    else if ( is_array( $key ) )
      foreach ( $key as $k => $v ) $this->data[$k] = $v;
    else
      $this->data[$key] = $value;

    return $this;
  }


  // Build path with format
  public function path( $format = null ) {
    return is_null( $format ) ? "/$this->path" : "/$this->path.$format";
  }


  // Get server with optional path
  public function source( $source = null ) {
    if ( is_null( $source ) ) {
      if ( $source = Source::get( $this->source ) )
        return preg_replace( '/\/$/', '', $source );

      throw new ModelMissingSourceException( "API source $this->source not defined" );
    }

    $this->source = $source;

    return $this;
  }


  // Set/get the secret
  public function secret( $secret = null ) {
    if ( is_null( $secret ) ) {
      if ( is_null( $this->secret ) ) return;
      
      if ( $secret = Secret::get( $this->secret ) )
        return $secret;

      throw new ModelMissingSecretException( "API secret $this->secret not defined" );
    }

    $this->secret = $secret;

    return $this;
  }


  // Get full uri; path with get params
  public function uri( $format = 'json' ) {
    $path = $this->path( $format );
    
    // add token
    if ( $this->secret ) {
      $token = new Token( $path, $this->secret );
      $this->query( 'token', $token->get() );
    }

    return empty( $this->query ) ? $path : "$path?" . http_build_query( $this->query );
  }


  // Get full url; server with path
  public function url( $format = 'json', $ssl = null ) {
    // get source url
    $source = $this->source();
    
    // force https
    if ( $ssl === true )
      $source = preg_replace( '/^http:/', 'https:', $source );

    // force http
    if ( $ssl === false )
      $source = preg_replace( '/^https:/', 'http:', $source );

    return $source . $this->uri( $format );
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
    
    return $response
      ->body( $request->body )
      ->status( $request->status_code )
      ->headers( $request->headers );
  }

}

// Exceptions
class ModelMissingSourceException extends Exception {}
class ModelMissingSecretException extends Exception {}