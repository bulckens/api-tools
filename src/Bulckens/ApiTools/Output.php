<?php

namespace Bulckens\ApiTools;

use Exception;
use Bulckens\Helpers\TimeHelper as Time;
use Bulckens\Helpers\ArrayHelper;

class Output {

  use Traits\Status;

  protected $output;
  protected $format;
  protected $headers;
  protected $options;
  protected $path;

  public function __construct( $format, $options = [] ) {
    $this->format  = $format;
    $this->options = $options;

    $this->clear();
  }


  // Add output
  public function add( $output ) {
    if ( ! is_array( $output ))
      throw new OutputArgumentInvalidException( 'Only an array is accepted' );

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
    array_push( $this->headers, [ $key, $value ]);

    return $this;
  }


  // Get headers
  public function headers() {
    return $this->headers;
  }


  // Set expires header
  public function expires( $lifetime = 3600 ) {
    return $this->header( 'Pragma', 'public' )
                ->header( 'Cache-Control', 'maxage=' . Time::sec( $lifetime ) )
                ->header( 'Expires', gmdate( 'D, d M Y H:i:s', time() + $lifetime ) . ' GMT' );
  }


  // Return mime type
  public function mime() {
    return Mime::type( $this->format );
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


  // Path getter/setter
  public function path( $path = null ) {
    if( is_null( $path ))
      return $this->path;

    $this->path = $path;

    return $this;
  }


  // Purify output based on status code
  public function purify() {
    if ( $this->ok() ) {
      unset( $this->output['error'] );

      if ( ! isset( $this->output['success'] ) )
        $this->output['success'] = "status.{$this->status()}";

    } else {
      foreach ( $this->output as $key => $value )
        if ( $key != 'error' && $key != 'details' )
          unset( $this->output[$key] );

      if ( ! isset( $this->output['error'] ) )
        $this->output['error'] = "status.{$this->status()}";
    }
    
  }


  // Render output to desired format
  public function render() {
    // make sure only reuired values are rendered
    $this->purify();

    // render output using user defined method
    if ( $method = Api::get()->config( 'methods.render' ) ) {
      if ( is_callable( $method ) ) {
        if ( $view = call_user_func( $method, $this ) )
          return $view;
      } else {
        throw new OutputRenderMethodNotCallableException( "Render method $method could not be found" );
      }
    }

    // render default output
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
      case 'array':
        return $this->toArray();
      break;
      case 'html':
      case 'txt':
      case 'css':
      case 'js':
        // return error if error is given
        if ( isset( $this->output['error'] ))
          return Mime::comment( "error: {$this->output['error']}", $this->format );

        if ( isset( $this->output['body'] ) ) {
          $body = $this->output['body'];
          unset( $this->output['body'] );

          // stringify body
          if ( is_array( $body ) ) $body = implode( "\n", $body );

          // add output status if verbose is set to true
          if ( Api::get()->config( 'verbose' ) )
            $body .= Mime::comment( print_r( $this->output, true ), $this->format );

          return $body;
        }

        return Mime::comment( print_r( $this->output, true ), $this->format );
      break;
      default:
        throw new OutputFormatUnknownException( "Unknown format {$this->format}" );
      break;
    }
  }
}


// Exceptions
class OutputFormatUnknownException            extends Exception {}
class OutputRenderMethodNotCallableException  extends Exception {}
class OutputArgumentInvalidException          extends Exception {}
