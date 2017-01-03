<?php

namespace Bulckens\ApiTools\V1;

use Exception;
use Bulckens\Helpers\ArrayHelper;

class Output {

  protected $output;
  protected $format;
  protected $status;
  protected $headers;
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
    $this->output  = [];
    $this->headers = [];

    return $this;
  }

  // Add header
  public function header( $key, $value ) {
    array_push( $this->header, [ $key, $value ]);

    return $this;
  }

  // Get headers
  public function headers() {
    return $this->headers;
  }

  // Set expires header
  public function expires( $lifetime = 3600 ) {
    return $this->header( 'Pragma', 'public' )
                ->header( 'Cache-Control', "maxage=$lifetime" )
                ->header( 'Expires', gmdate( 'D, d M Y H:i:s', time() + $lifetime ) . ' GMT' );
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

  // Get current output array
  public function toArray() {
    return $this->output;
  }

  // Data type tester
  public function is( $format ) {
    return $this->format == $format;
  }

  // Format getter
  public function format() {
    return $this->format;
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
        // return error if error is given
        if ( isset( $this->output['error'] ))
          return Mime::comment( $this->format, "error: {$this->output['error']}" );

        if ( isset( $this->output['body'] ) ) {
          $body = $this->output['body'];
          unset( $this->output['body'] );

          // stringify body
          if ( is_array( $body ) ) $body = implode( "\n", $body );

          // add output status if verbose is set to true
          if ( Config::get( 'verbose' ) )
            $body .= Mime::comment( $this->format, print_r( $this->output, true ) );

          return $body;
        }

        return Mime::comment( $this->format, print_r( $this->output, true ) );
      break;
      default:
        throw new UnknownFormatException( "Unknown format {$this->format}" );
      break;
    }
  }
}


// Exceptions
class UnknownFormatException extends Exception {}
