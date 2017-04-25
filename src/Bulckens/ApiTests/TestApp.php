<?php 

namespace Bulckens\ApiTests;

class TestApp {

  // Parent app environment method
  public static function env() {
    return 'dev';
  }

  // Parent app secret retreiver method
  public static function secret( $key ) {
    switch ( $key ) {
      case 'generic':
        return 'fallalifallala';
      break;
      case 'mastaba':
        return 'jhBw45b534Jb53';
      break;
    }
  }

}