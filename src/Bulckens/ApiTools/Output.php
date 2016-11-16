<?php

namespace Bulckens\ApiTools;

class Output {
  
  public function __construct( $format, $options = [] ) {
    $this->output  = [];
    $this->format  = $format;
    $this->status  = 200;
    $this->options = $options;

    // build map
    $this->map = [
      'json' => 'application/json'
    , 'xml'  => 'application/xml'
    , 'dump' => 'text/plain'
    ];
  }

  // Render an error
  public static function error( $message, $format, $code = 500 ) {
    switch ( $format ) {
      case 'cli':
      case 'xml':
        return Twig::render( 'cli/error.xml', [
          'message' => $message
        ]);
      break;
      case 'json':
        return json_encode( [ 'error' => $message ] );
      break;
      case 'html':
        $env = Environment::where( 'name', Session::chili( 'env' ) )->first();

        return Twig::render( 'error.html', [
          'code'  => $code
        , 'image' => $env->error_file_name
        ]);
      break;
    }
  }

  // Convert key to mime type
  public static function mime( $format ) {
    switch ( $format ) {
      case 'xml':
        return 'application/xml';
      break;
      case 'json':
        return 'application/json';
      break;
      default:
        return 'text/html';
      break;
    }
  }

}

