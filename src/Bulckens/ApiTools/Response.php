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
  public function attr( $key ) {
    if ( isset( $this->parse()[$key] ) )
      return $this->parse()[$key];
  }

}
