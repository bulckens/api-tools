<?php

namespace Bulckens\ApiTools;

use Bulckens\Helpers\ArrayHelper;

class Response {

  use Traits\Status;

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
