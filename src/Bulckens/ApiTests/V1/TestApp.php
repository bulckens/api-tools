<?php 

namespace Bulckens\ApiTests\V1;

class TestApp {

  // Parent app environment method
  public static function env() {
    return 'dev';
  }

  // Parent app render method
  public static function render( $output ) {
    switch ( $output->format() ) {
      case 'html':
        return '<html><head><title></title></head><body>Rendered from the outside!</body></html>';
      break;
    }
  }

  // Parent app secret retreiver method
  public static function secret( $key ) {
    return 'fallalifallala';
  }

}