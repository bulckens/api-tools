<?php 

namespace Bulckens\ApiTools;

class RawRequest extends Model {

  // Initialize new instance
  public function __construct( $source, $secret = null ) {
    $this->source( $source );

    if ( ! is_null( $secret ) )
      $this->secret( $secret );
  }


  // Get server with optional path
  public function source( $source = null ) {
    if ( is_null( $source ) ) {
      return preg_replace( '/\/$/', '', $this->source );
    }

    $this->source = $source;

    return $this;
  }


  // Set/get the secret
  public function secret( $secret = null ) {
    if ( is_null( $secret ) ) {
      return $this->secret;
    }

    $this->secret = $secret;

    return $this;
  }


  // Build token for format
  protected function buildToken( $path ) {
    return new RawToken( $path, $this->secret );
  }

}