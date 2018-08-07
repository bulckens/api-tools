<?php 

namespace Bulckens\ApiTests;

class TestApp {

  // Environment method
  public static function env() {
    return 'dev';
  }


  // Secret retreiver method
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

  // Source retreiver method
  public static function source( $key ) {
    switch ( $key ) {
      case 'alternative':
        return 'http://alternative.com';
      break;
      case 'mastaba':
        return 'http://wag.com';
      break;
    }
  }

}