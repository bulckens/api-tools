<?php

namespace Bulckens\ApiTools;

use Exception;
use Bulckens\Helpers\TimeHelper;
use Bulckens\AppTools\Traits\Tokenish;

class Token {

  use Tokenish;
  
  protected $uri;

  public function __construct( $uri, $secret, $stamp = null ) {
    // store uri
    $this->uri = $uri;

    // store or generate timestamp
    $this->stampless = is_null( $stamp );
    $this->stamp = $stamp ?: TimeHelper::ms();
    
    // find secret
    $this->secret = Secret::get( $secret );

    // make sure the given secret exists
    $this->verify( $secret );
  }


  // Get generated token
  public function get() {
    return $this->hash([ $this->secret, $this->stamp, $this->uri ]);
  }


  // URI getter
  public function uri() {
    return $this->uri;
  }

}


// Exceptions
class TokenSecretMissingException extends Exception {}
