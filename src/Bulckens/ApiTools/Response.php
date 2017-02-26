<?php

namespace Bulckens\ApiTools;

use Bulckens\Helpers\ArrayHelper;

class Response {

  use Traits\Status;

  protected $format;
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
    switch ( $this->format ) {
      case 'json':
        return ArrayHelper::fromJson( $this->body );
      break;
      case 'xml':
        return ArrayHelper::fromXml( $this->body );
      break;
      case 'yaml':
        return ArrayHelper::fromYaml( $this->body );
      break;
      default:
        return $this->body;
      break;
    }
  }

}
