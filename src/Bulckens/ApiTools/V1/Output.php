<?php

namespace Bulckens\ApiTools\V1;

use Exception;
use Bulckens\Helpers\ArrayHelper;

class Output {

  protected $output;
  protected $format;
  protected $status;
  protected $options;
  protected $map;

  public function __construct( $format, $options = [] ) {
    $this->output  = [];
    $this->format  = $format;
    $this->status  = 200;
    $this->options = $options;
  }

  // Add output status
  public function add( $output ) {
    $this->output = array_replace_recursive( $this->output, $output );

    return $this;
  }

  // Return mime type
  public function mime() {
    return Config::mime( $this->format );
  }

  // Return status code
  public function status( $status = null ) {
    // act as getter
    if ( is_null( $status ) )
      return $this->status;

    // act as setter
    $this->status = $status;

    return $this;
  }

  // Check of status code is ok
  public function ok() {
    return $this->status == 200;
  }

  // Render output to desired format
  public function render() {
    switch ( $this->format ) {
      case 'json':
        return ArrayHelper::toJson( $this->output );
      break;
      
      case 'yaml':
        return ArrayHelper::toYaml( $this->output );
      break;
      
      case 'xml':
        return ArrayHelper::toXml( $this->output, $this->options );
      break;

      case 'html':
        return $this->output;
      break;
      
      case 'dump':
        return print_r( $this->output, true );
      break;

      default:
        throw new UnknownFormatException( "Unknown format {$this->format}" );
      break;
    }
  }

  // Data type tester
  public function is( $format ) {
    return $this->format == $format;
  }

  // Format getter
  public function format() {
    return $this->format;
  }
}


// Exceptions
class UnknownFormatException extends Exception {}
