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
    $this->format  = $format;
    $this->status  = 200;
    $this->options = $options;

    $this->clear();
  }

  // Add output
  public function add( $output ) {
    $this->output = array_replace_recursive( $this->output, $output );

    return $this;
  }

  // Clear all output
  public function clear() {
    $this->output = [];

    return $this;
  }

  // Return mime type
  public function mime() {
    return Mime::type( $this->format );
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

  // Check of status code is ok (everything within the 200 and 300 codes)
  public function ok() {
    return $this->status < 400;
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
      case 'dump':
        return print_r( $this->output, true );
      break;
      case 'html':
      case 'txt':
      case 'css':
      case 'js':
        if ( is_array( $this->output[$this->format] ) )
          return implode( "\n", $this->output[$this->format] );
        else
          return $this->output[$this->format];
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
