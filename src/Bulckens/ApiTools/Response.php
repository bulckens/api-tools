<?php

namespace Bulckens\ApiTools;

use Bulckens\Helpers\ArrayHelper;
use Bulckens\AppTools\Traits\Status;

class Response {

  use Status;

  protected $headers;
  protected $format;
  protected $cache;
  protected $body;

  public function __construct( $format ) {
    $this->format = $format;
  }


  // Returns stored format
  public function format( $format = null ) {
    if ( is_string( $format ) )
      return $format == $this->format;

    if ( is_array( $format ) )
      return in_array( $this->format, $format );

    return $this->format;
  }


  // Set/get headers
  public function headers( $headers = null ) {
    if ( is_null( $headers ) )
      return $this->headers;

    if ( is_string( $headers ) )
      return isset( $this->headers[$headers] ) ? $this->headers[$headers] : null;

    $this->headers = $headers;

    return $this;
  }


  // Returns stored body
  public function body( $body = null ) {
    if ( is_null( $body ) )
      return $this->body;

    $this->body = $body;

    return $this;
  }


  // Parse body
  public function parse() {
    // cache parsed body
    if ( is_null( $this->cache ) ) {
      switch ( $this->format ) {
        case 'json':
          $this->cache = ArrayHelper::fromJson( $this->body );
        break;
        case 'xml':
          $this->cache = ArrayHelper::fromXml( $this->body );
        break;
        case 'yaml':
          $this->cache = ArrayHelper::fromYaml( $this->body );
        break;
        default:
          $this->cache = $this->body;
        break;
      }
    }
    
    return $this->cache;
  }


  // Get attribute from parsed body
  public function attr( $key, $default = null ) {
    // prepare path iteration
    $parts = explode( '.', $key );
    $value = $this->parse();

    // find value for path
    foreach ( $parts as $part ) {
      // simply get value
      if ( isset( $value[$part] ) )
        $value = $value[$part];

      // get the last item of a collection
      elseif ( is_array( $value ) && $part == '#n' )
        $value = end( $value );

      // get an item at a given position in a collection
      elseif ( is_array( $value ) && preg_match( '/^#(\d+)$/', $part, $m ) && isset( $value[$m[1]] ) )
        $value = $value[$m[1]];

      // collect attribute values
      elseif ( is_array( $value ) && preg_match( '/^@([a-zA-Z0-9_-]+)$/', $part, $m ) )
        $value = array_map( function( $v ) use( $m ) { return $v[$m[1]]; }, $value );

      // return absolute fallback value
      else return $default;
    }

    return $value;
  }

}
